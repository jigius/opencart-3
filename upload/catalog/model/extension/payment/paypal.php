<?php
class ModelExtensionPaymentPayPal extends Model {
    public function getMethod(array $address): array {
        $this->load->language('extension/payment/paypal');

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_paypal_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

        if (!$this->config->get('payment_paypal_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = [];

        if ($status) {
            $method_data = [
                'code'       => 'paypal',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_paypal_sort_order')
            ];
        }

        return $method_data;
    }

    public function log($data, $title = null) {
        if ($this->config->get('payment_paypal_debug')) {
            // Log
            $log = new \Log('paypal.log');
            $log->write('PayPal debug (' . $title . '): ' . json_encode($data));
        }
    }
}