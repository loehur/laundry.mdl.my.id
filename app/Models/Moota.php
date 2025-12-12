<?php
class Moota
{
    private $token = 'YOUR_API_TOKEN_HERE';
    private $apiUrl = 'https://api.moota.co/api/v2/create-transaction';

    public function __construct()
    {
        // Allow configuring token if needed, or leave hardcoded placeholder
    }

    /**
     * Create a transaction in Moota V2.
     * 
     * @param array $data The payload body as an associative array.
     *                    Structure example:
     *                    [
     *                      "order_id" => "xxxxx",
     *                      "bank_account_id" => "xxxxx",
     *                      "customers" => ["name" => "...", "email" => null, "phone" => null],
     *                      "items" => [ ["name" => "...", "qty" => 1, "price" => 10000] ],
     *                      "description" => "...",
     *                      "note" => null,
     *                      "redirect_url" => "...",
     *                      "total" => 10000
     *                    ]
     * @return string|bool API response or false on failure
     */
    public function createTransaction($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Location: /api/v2/mutation-tracking', // Per user request
                'Accept: application/json',
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json' // Usually required for JSON body
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_encode(['error' => 'CURL Error: ' . $err]);
        }

        return $response;
    }
}
