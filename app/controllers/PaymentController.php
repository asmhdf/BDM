<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentController {
    private $pdo;
    public function __construct($pdo = null) { $this->pdo = $pdo; }

    private function paypalClient() {
        if (!class_exists(PayPalHttpClient::class)) {
            throw new \RuntimeException('PayPal SDK not installed. Run: composer require paypal/paypal-checkout-sdk');
        }
        $clientId = 'AXbuLfkYCEO41t96JXmTjzRdOe5vcKqChXmRke1qYKpNtDbe66lbxdGK-bprCXqOi61wfIJg5gaunO_L';
        $clientSecret = 'EHmt9KLLXv2dk7QUmyPm9hkFvTjD30XfsIaxy_i5T6cxPp18cJpVj2RSSTwSC6X3Amg7cP7-QnEFiarD';
        $mode =  'sandbox';
        $env = ($mode === 'live') ? new ProductionEnvironment($clientId, $clientSecret) : new SandboxEnvironment($clientId, $clientSecret);
        return new PayPalHttpClient($env);
    }

    public function paymentPage() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $orderId = $_GET['order_id'] ?? null;
        if (!$orderId) { header('Location: index.php?action=panier'); exit; }
        $order = null; $total = 0;
        if ($this->pdo) {
            $stmt = $this->pdo->prepare("SELECT * FROM commandes WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($order) $total = number_format($order['total'], 2, '.', '');
        }
        require __DIR__ . '/../views/payment.php';
    }

    public function createPayPalOrder() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents('php://input'), true);
        $localOrderId = $input['order_id'] ?? null;
        if (!$localOrderId) { http_response_code(400); echo json_encode(['error'=>'missing_order']); return; }
        if (!$this->pdo) { http_response_code(500); echo json_encode(['error'=>'no_db']); return; }
        $stmt = $this->pdo->prepare("SELECT total FROM commandes WHERE id = :id LIMIT 1");
        $stmt->execute([':id'=>$localOrderId]);
        $o = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$o) { http_response_code(404); echo json_encode(['error'=>'order_not_found']); return; }
        $amount = number_format($o['total'], 2, '.', '');
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => (string)$localOrderId,
                "amount" => ["currency_code" => "EUR", "value" => $amount],
                "description" => "Commande #{$localOrderId}"
            ]]
        ];
        try {
            $client = $this->paypalClient();
            $response = $client->execute($request);
            echo json_encode(['id' => $response->result->id]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function capturePayPalOrder() {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents('php://input'), true);
        $payOrderId = $input['orderID'] ?? null;
        $localOrderId = $input['local_order_id'] ?? null;
        if (!$payOrderId || !$localOrderId) { http_response_code(400); echo json_encode(['error'=>'missing_params']); return; }
        try {
            $client = $this->paypalClient();
            $request = new OrdersCaptureRequest($payOrderId);
            $request->prefer('return=representation');
            $response = $client->execute($request);
            if ($this->pdo) {
                // update commande statut -> 'payee'
                $stmt = $this->pdo->prepare("UPDATE commandes SET statut = 'payee' WHERE id = :id");
                $stmt->execute([':id' => $localOrderId]);

                // insert a record in paiements (montant  mode = 'paypal')
                // retrieve amount from commande to avoid trusting client
                $s = $this->pdo->prepare("SELECT total FROM commandes WHERE id = :id LIMIT 1");
                $s->execute([':id' => $localOrderId]);
                $row = $s->fetch(PDO::FETCH_ASSOC);
                $montant = $row ? $row['total'] : 0;

                $pstmt = $this->pdo->prepare(
                    "INSERT INTO paiements (commande_id, montant, mode, statut, date_paiement)
                     VALUES (:cid, :montant, 'paypal', 'valide', NOW())"
                );
                $pstmt->execute([':cid' => $localOrderId, ':montant' => $montant]);

                // clear cart session
                if (session_status() === PHP_SESSION_NONE) session_start();
                unset($_SESSION['cart']);
            }
            echo json_encode(['success' => true, 'paypal' => json_decode(json_encode($response->result), true)]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}