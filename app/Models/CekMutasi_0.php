<?php

class CekMutasi
{
    public function cek($bank, $amount)
    {
        $cek = 0;
        $phpDate = date('m') . "/" . date('d') . "/" . date('Y');
        $phpDate = strtotime($phpDate);
        $lastWeek = strtotime("-7 day", $phpDate);
        $lastWeek = date('Y-m-d', $lastWeek);
        $today = date('Y-m-d');

        $bankID = "";
        if ($bank == 'bca') {
            $bankID = "bpPkBYreWB2";
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://app.moota.co/api/v2/mutation?type=cr&bank=' . $bankID . '&amount=' . $amount . '&start_date=' . $lastWeek . '&end_date=' . $today);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJucWllNHN3OGxsdyIsImp0aSI6ImViZThiYzQ5ODQ3MDhlOWZkMTc1ZjE4YTlkODc5OTY5YTA5MTQxNmM2OTcyZjlhNWQ1YWUxOWNkZWNkY2U3N2U0ZTMzZGRiNjE2ODExYmYxIiwiaWF0IjoxNjQ1MDk3ODYyLjEzMjM4LCJuYmYiOjE2NDUwOTc4NjIuMTMyMzgyLCJleHAiOjE2NzY2MzM4NjIuMTMxMjY0LCJzdWIiOiIyNzc0Iiwic2NvcGVzIjpbIm11dGF0aW9uX3JlYWQiLCJhcGkiXX0.Hp3qnP0EqCbjfep7Xd1Pq72hE5pvpUUT4a41DpOKW83HY10aEhmSqhKkdlPUoLnjz-QYMtBMwzjeLMU9mXVbucQ0E9HNMv4M1omMV6-YohO9Riup0T2g95i_HATx7U_iQ4Z2xyk8q8XXEEaJrX93LyoHEMpXz75EzUr4_3Fbjm39g1yKspy7RcSMRBuF0HnTNeSbEMQvBprPpbBpaiS8EMza6xxHa6hT0EgM1XAKZ-XktvlVGRgHAIpxMSY2nzlX6ZhQ-etCw8bUa2K5CRGzBfGpVxztgW8TpavqntNZXYKLhr8la9Dqpnh04jDGUqMNUy_7e4HXs1FJKE_z_M32pTjzWU7f5VS-dTPzhADgdwcYghhnMH1sxTyxXoY7VXhWO-UKwp7L_Ca7zhL9yL7aPnbIccCc5w16A1h7xFhqUOC4sOnbM3Ci7BfbkM_9fZPKkJ5wGcVdhfzCWLT8dQJhRlf7hMdVWBaAE_N-CEQ6qf6nvdI1CoJaIhY9DQljkYaIKGF8rfDQZiOVYawo8V0MF5wynW1WO75prcaIsmBNergngjYXRlvYikgdb2SmYg_ZSc1Q_sgsYISnZD3zPXYGrodc5zpM687Q12NTcMcuyfB7DlfYzDFIDkYqnlZgPFa_AUY6EoIh9gwmLaAr0KDJ8CB29iUVp97m081LLeOnDfw'
        ]);
        $response = json_decode(curl_exec($curl), true);
        $data = $response['data'][0];
        if (isset($data)) {
            $jumlah = $data['amount'];
        } else {
            $jumlah = NULL;
        }
        if ($jumlah == $amount) {
            $cek = 1;
        }
        return $cek;
    }
}
