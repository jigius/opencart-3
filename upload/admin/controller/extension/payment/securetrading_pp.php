<?php
class ControllerExtensionPaymentSecureTradingPp extends Controller {
    private array $error = [];

    public function index(): void {
        $this->load->language('extension/payment/securetrading_pp');

        // Settings
        $this->load->model('setting/setting');

        // Geo Zones
        $this->load->model('localisation/geo_zone');

        // Order Statuses
        $this->load->model('localisation/order_status');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['payment_securetrading_pp_site_reference'] = $this->request->post['payment_securetrading_pp_site_reference'];

            $this->model_setting_setting->editSetting('payment_securetrading_pp', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        if (isset($this->request->post['payment_securetrading_pp_site_reference'])) {
            $data['payment_securetrading_pp_site_reference'] = $this->request->post['payment_securetrading_pp_site_reference'];
        } else {
            $data['payment_securetrading_pp_site_reference'] = $this->config->get('payment_securetrading_pp_site_reference');
        }

        if (isset($this->request->post['payment_securetrading_pp_version'])) {
            $data['payment_securetrading_pp_version'] = $this->request->post['payment_securetrading_pp_version'];
        } else {
            $data['payment_securetrading_pp_version'] = $this->config->get('payment_securetrading_pp_version');
        }

        if (isset($this->request->post['payment_securetrading_pp_username'])) {
            $data['payment_securetrading_pp_username'] = $this->request->post['payment_securetrading_pp_username'];
        } else {
            $data['payment_securetrading_pp_username'] = $this->config->get('payment_securetrading_pp_username');
        }

        if (isset($this->request->post['payment_securetrading_pp_password'])) {
            $data['payment_securetrading_pp_password'] = $this->request->post['payment_securetrading_pp_password'];
        } else {
            $data['payment_securetrading_pp_password'] = $this->config->get('payment_securetrading_pp_password');
        }

        if (isset($this->request->post['payment_securetrading_pp_notification_password'])) {
            $data['payment_securetrading_pp_notification_password'] = $this->request->post['payment_securetrading_pp_notification_password'];
        } else {
            $data['payment_securetrading_pp_notification_password'] = $this->config->get('payment_securetrading_pp_notification_password');
        }

        if (isset($this->request->post['payment_securetrading_pp_site_security_password'])) {
            $data['payment_securetrading_pp_site_security_password'] = $this->request->post['payment_securetrading_pp_site_security_password'];
        } else {
            $data['payment_securetrading_pp_site_security_password'] = $this->config->get('payment_securetrading_pp_site_security_password');
        }

        if (isset($this->request->post['payment_securetrading_pp_site_security_status'])) {
            $data['payment_securetrading_pp_site_security_status'] = $this->request->post['payment_securetrading_pp_site_security_status'];
        } else {
            $data['payment_securetrading_pp_site_security_status'] = $this->config->get('payment_securetrading_pp_site_security_status');
        }

        if (isset($this->request->post['payment_securetrading_pp_webservice_username'])) {
            $data['payment_securetrading_pp_webservice_username'] = $this->request->post['payment_securetrading_pp_webservice_username'];
        } else {
            $data['payment_securetrading_pp_webservice_username'] = $this->config->get('payment_securetrading_pp_webservice_username');
        }

        if (isset($this->request->post['payment_securetrading_pp_webservice_password'])) {
            $data['payment_securetrading_pp_webservice_password'] = $this->request->post['payment_securetrading_pp_webservice_password'];
        } else {
            $data['payment_securetrading_pp_webservice_password'] = $this->config->get('payment_securetrading_pp_webservice_password');
        }

        if (isset($this->request->post['payment_securetrading_pp_order_status_id'])) {
            $data['payment_securetrading_pp_order_status_id'] = (int)$this->request->post['payment_securetrading_pp_order_status_id'];
        } elseif ($this->config->get('payment_securetrading_pp_order_status_id') != '') {
            $data['payment_securetrading_pp_order_status_id'] = $this->config->get('payment_securetrading_pp_order_status_id');
        } else {
            $data['payment_securetrading_pp_order_status_id'] = 1;
        }

        if (isset($this->request->post['payment_securetrading_pp_declined_order_status_id'])) {
            $data['payment_securetrading_pp_declined_order_status_id'] = (int)$this->request->post['payment_securetrading_pp_declined_order_status_id'];
        } elseif ($this->config->get('payment_securetrading_pp_declined_order_status_id') != '') {
            $data['payment_securetrading_pp_declined_order_status_id'] = $this->config->get('payment_securetrading_pp_declined_order_status_id');
        } else {
            $data['payment_securetrading_pp_declined_order_status_id'] = 8;
        }

        if (isset($this->request->post['payment_securetrading_pp_refunded_order_status_id'])) {
            $data['payment_securetrading_pp_refunded_order_status_id'] = (int)$this->request->post['payment_securetrading_pp_refunded_order_status_id'];
        } elseif ($this->config->get('payment_securetrading_pp_refunded_order_status_id') != '') {
            $data['payment_securetrading_pp_refunded_order_status_id'] = $this->config->get('payment_securetrading_pp_refunded_order_status_id');
        } else {
            $data['payment_securetrading_pp_refunded_order_status_id'] = 11;
        }

        if (isset($this->request->post['payment_securetrading_pp_authorisation_reversed_order_status_id'])) {
            $data['payment_securetrading_pp_authorisation_reversed_order_status_id'] = (int)$this->request->post['payment_securetrading_pp_authorisation_reversed_order_status_id'];
        } elseif ($this->config->get('payment_securetrading_pp_authorisation_reversed_order_status_id') != '') {
            $data['payment_securetrading_pp_authorisation_reversed_order_status_id'] = $this->config->get('payment_securetrading_pp_authorisation_reversed_order_status_id');
        } else {
            $data['payment_securetrading_pp_authorisation_reversed_order_status_id'] = 12;
        }

        if (isset($this->request->post['payment_securetrading_pp_settle_status'])) {
            $data['payment_securetrading_pp_settle_status'] = $this->request->post['payment_securetrading_pp_settle_status'];
        } else {
            $data['payment_securetrading_pp_settle_status'] = $this->config->get('payment_securetrading_pp_settle_status');
        }

        if (isset($this->request->post['payment_securetrading_pp_settle_due_date'])) {
            $data['payment_securetrading_pp_settle_due_date'] = $this->request->post['payment_securetrading_pp_settle_due_date'];
        } else {
            $data['payment_securetrading_pp_settle_due_date'] = $this->config->get('payment_securetrading_pp_settle_due_date');
        }

        if (isset($this->request->post['payment_securetrading_pp_geo_zone_id'])) {
            $data['payment_securetrading_pp_geo_zone_id'] = (int)$this->request->post['payment_securetrading_pp_geo_zone_id'];
        } else {
            $data['payment_securetrading_pp_geo_zone_id'] = $this->config->get('payment_securetrading_pp_geo_zone_id');
        }

        if (isset($this->request->post['payment_securetrading_pp_status'])) {
            $data['payment_securetrading_pp_status'] = $this->request->post['payment_securetrading_pp_status'];
        } else {
            $data['payment_securetrading_pp_status'] = $this->config->get('payment_securetrading_pp_status');
        }

        if (isset($this->request->post['payment_securetrading_pp_sort_order'])) {
            $data['payment_securetrading_pp_sort_order'] = $this->request->post['payment_securetrading_pp_sort_order'];
        } else {
            $data['payment_securetrading_pp_sort_order'] = $this->config->get('payment_securetrading_pp_sort_order');
        }

        if (isset($this->request->post['payment_securetrading_pp_total'])) {
            $data['payment_securetrading_pp_total'] = $this->request->post['payment_securetrading_pp_total'];
        } else {
            $data['payment_securetrading_pp_total'] = $this->config->get('payment_securetrading_pp_total');
        }

        if (isset($this->request->post['payment_securetrading_pp_parent_css'])) {
            $data['payment_securetrading_pp_parent_css'] = $this->request->post['payment_securetrading_pp_parent_css'];
        } else {
            $data['payment_securetrading_pp_parent_css'] = $this->config->get('payment_securetrading_pp_parent_css');
        }

        if (isset($this->request->post['payment_securetrading_pp_child_css'])) {
            $data['payment_securetrading_pp_child_css'] = $this->request->post['payment_securetrading_pp_child_css'];
        } else {
            $data['payment_securetrading_pp_child_css'] = $this->config->get('payment_securetrading_pp_child_css');
        }

        if (isset($this->request->post['payment_securetrading_pp_cards_accepted'])) {
            $data['payment_securetrading_pp_cards_accepted'] = $this->request->post['payment_securetrading_pp_cards_accepted'];
        } else {
            $data['payment_securetrading_pp_cards_accepted'] = $this->config->get('payment_securetrading_pp_cards_accepted');

            if ($data['payment_securetrading_pp_cards_accepted'] == null) {
                $data['payment_securetrading_pp_cards_accepted'] = [];
            }
        }

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['site_reference'])) {
            $data['error_site_reference'] = $this->error['site_reference'];
        } else {
            $data['error_site_reference'] = '';
        }

        if (isset($this->error['version'])) {
            $data['error_version'] = $this->error['version'];
        } else {
            $data['error_version'] = '';
        }

        if (isset($this->error['cards_accepted'])) {
            $data['error_cards_accepted'] = $this->error['cards_accepted'];
        } else {
            $data['error_cards_accepted'] = '';
        }

        if (isset($this->error['notification_password'])) {
            $data['error_notification_password'] = $this->error['notification_password'];
        } else {
            $data['error_notification_password'] = '';
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/securetrading_pp', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['cards'] = [
            'AMEX'            => 'American Express',
            'VISA'            => 'Visa',
            'DELTA'           => 'Visa Debit',
            'ELECTRON'        => 'Visa Electron',
            'PURCHASING'      => 'Visa Purchasing',
            'VPAY'            => 'V Pay',
            'MASTERCARD'      => 'MasterCard',
            'MASTERCARDDEBIT' => 'MasterCard Debit',
            'MAESTRO'         => 'Maestro',
            'PAYPAL'          => 'PayPal'
        ];

        $data['settlement_statuses'] = [
            '0'   => $this->language->get('text_pending_settlement'),
            '1'   => $this->language->get('text_pending_settlement_manually_overridden'),
            '2'   => $this->language->get('text_pending_suspended'),
            '100' => $this->language->get('text_pending_settled'),
        ];

        $data['action'] = $this->url->link('extension/payment/securetrading_pp', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/securetrading_pp', $data));
    }

    public function install(): void {
        // Securetrading PP
        $this->load->model('extension/payment/securetrading_pp');

        $this->model_extension_payment_securetrading_pp->install();
    }

    public function uninstall(): void {
        // Securetrading PP
        $this->load->model('extension/payment/securetrading_pp');

        $this->model_extension_payment_securetrading_pp->uninstall();
    }

    public function order(): string {
        if ($this->config->get('payment_securetrading_pp_status')) {
            // Securetrading PP
            $this->load->model('extension/payment/securetrading_pp');

            $securetrading_pp_order = $this->model_extension_payment_securetrading_pp->getOrder($this->request->get['order_id']);

            if (!empty($securetrading_pp_order)) {
                $this->load->language('extension/payment/securetrading_pp');

                $securetrading_pp_order['total_released'] = $this->model_extension_payment_securetrading_pp->getTotalReleased($securetrading_pp_order['securetrading_pp_order_id']);
                $securetrading_pp_order['total_formatted'] = $this->currency->format($securetrading_pp_order['total'], $securetrading_pp_order['currency_code'], false, false);
                $securetrading_pp_order['total_released_formatted'] = $this->currency->format($securetrading_pp_order['total_released'], $securetrading_pp_order['currency_code'], false, false);

                $data['securetrading_pp_order'] = $securetrading_pp_order;
                $data['auto_settle'] = $securetrading_pp_order['settle_type'];

                $data['order_id'] = (int)$this->request->get['order_id'];

                $data['user_token'] = $this->session->data['user_token'];

                return $this->load->view('extension/payment/securetrading_pp_order', $data);
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function void(): void {
        $this->load->language('extension/payment/securetrading_pp');

        $json = [];

        if (isset($this->request->post['order_id']) && $this->request->post['order_id'] != '') {
            // Securetrading PP
            $this->load->model('extension/payment/securetrading_pp');

            $securetrading_pp_order = $this->model_extension_payment_securetrading_pp->getOrder($this->request->post['order_id']);
            $void_response = $this->model_extension_payment_securetrading_pp->void($this->request->post['order_id']);

            $this->model_extension_payment_securetrading_pp->logger('Void result:\r\n' . print_r($void_response, 1));

            if ($void_response !== false) {
                $response_xml = simplexml_load_string($void_response);

                if ($response_xml->response['type'] == 'ERROR' || (string)$response_xml->response->error->code != '0') {
                    $json['msg'] = (string)$response_xml->response->error->message;
                    $json['error'] = true;
                } else {
                    $this->model_extension_payment_securetrading_pp->addTransaction($securetrading_pp_order['securetrading_pp_order_id'], 'reversed', 0.00);
                    $this->model_extension_payment_securetrading_pp->updateVoidStatus($securetrading_pp_order['securetrading_pp_order_id'], 1);

                    $this->data = [
                        'order_status_id' => $this->config->get('payment_securetrading_pp_authorisation_reversed_order_status_id'),
                        'notify'          => false,
                        'comment'         => '',
                    ];

                    $this->load->model('sale/order');

                    $this->model_sale_order->addOrderHistory($this->request->post['order_id'], $this->data);

                    $json['msg'] = $this->language->get('text_authorisation_reversed');
                    $json['data']['created'] = date('Y-m-d H:i:s');

                    $json['error'] = false;
                }
            } else {
                $json['msg'] = $this->language->get('error_connection');

                $json['error'] = true;
            }
        } else {
            $json['msg'] = $this->language->get('error_data_missing');

            $json['error'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function release(): void {
        $this->load->language('extension/payment/securetrading_pp');

        $json = [];

        $amount = number_format($this->request->post['amount'], 2);

        if (isset($this->request->post['order_id']) && $this->request->post['order_id'] != '' && isset($amount) && $amount > 0) {
            // Securetrading PP
            $this->load->model('extension/payment/securetrading_pp');

            $securetrading_pp_order = $this->model_extension_payment_securetrading_pp->getOrder($this->request->post['order_id']);

            $release_response = $this->model_extension_payment_securetrading_pp->release($this->request->post['order_id'], $amount);
            $this->model_extension_payment_securetrading_pp->logger('Release result:\r\n' . print_r($release_response, 1));

            if ($release_response !== false) {
                $response_xml = simplexml_load_string($release_response);

                if ($response_xml->response['type'] == 'ERROR' || (string)$response_xml->response->error->code != '0') {
                    $json['msg'] = (string)$response_xml->response->error->message;

                    $json['error'] = true;
                } else {
                    $this->model_extension_payment_securetrading_pp->addTransaction($securetrading_pp_order['securetrading_pp_order_id'], 'payment', $amount);

                    $total_released = $this->model_extension_payment_securetrading_pp->getTotalReleased($securetrading_pp_order['securetrading_pp_order_id']);

                    if ($total_released >= $securetrading_pp_order['total'] || $securetrading_pp_order['settle_type'] == 100) {
                        $this->model_extension_payment_securetrading_pp->updateReleaseStatus($securetrading_pp_order['securetrading_pp_order_id'], 1);

                        $release_status = 1;
                        $json['msg'] = $this->language->get('text_release_ok_order');

                        $this->load->model('sale/order');

                        $history = [];

                        $history['order_status_id'] = $this->config->get('securetrading_pp_order_status_success_settled_id');
                        $history['comment'] = '';
                        $history['notify'] = '';

                        $this->model_sale_order->addOrderHistory($this->request->post['order_id'], $history);
                    } else {
                        $release_status = 0;

                        $json['msg'] = $this->language->get('text_release_ok');
                    }

                    $json['data'] = [];
                    $json['data']['created'] = date('Y-m-d H:i:s');
                    $json['data']['amount'] = $amount;
                    $json['data']['release_status'] = $release_status;
                    $json['data']['total'] = (double)$total_released;

                    $json['error'] = false;
                }
            } else {
                $json['msg'] = $this->language->get('error_connection');

                $json['error'] = true;
            }
        } else {
            $json['msg'] = $this->language->get('error_data_missing');

            $json['error'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function rebate(): void {
        $this->load->language('extension/payment/securetrading_pp');

        $json = [];

        if (isset($this->request->post['order_id'])) {
            // Securetrading PP
            $this->load->model('extension/payment/securetrading_pp');

            $securetrading_pp_order = $this->model_extension_payment_securetrading_pp->getOrder($this->request->post['order_id']);

            $amount = number_format($this->request->post['amount'], 2);

            $rebate_response = $this->model_extension_payment_securetrading_pp->rebate($this->request->post['order_id'], $amount);

            $this->model_extension_payment_securetrading_pp->logger('Rebate result:\r\n' . print_r($rebate_response, 1));

            if ($rebate_response !== false) {
                $response_xml = simplexml_load_string($rebate_response);

                $error_code = (string)$response_xml->response->error->code;

                if ($error_code == '0') {
                    $this->model_extension_payment_securetrading_pp->addTransaction($securetrading_pp_order['securetrading_pp_order_id'], 'rebate', $amount * -1);

                    $total_rebated = $this->model_extension_payment_securetrading_pp->getTotalRebated($securetrading_pp_order['securetrading_pp_order_id']);
                    $total_released = $this->model_extension_payment_securetrading_pp->getTotalReleased($securetrading_pp_order['securetrading_pp_order_id']);

                    if ($total_released <= 0 && $securetrading_pp_order['release_status'] == 1) {
                        $json['status'] = 1;

                        $json['message'] = $this->language->get('text_refund_issued');

                        $this->model_extension_payment_securetrading_pp->updateRebateStatus($securetrading_pp_order['securetrading_pp_order_id'], 1);

                        $rebate_status = 1;
                        $json['msg'] = $this->language->get('text_rebate_ok_order');

                        // Orders
                        $this->load->model('sale/order');

                        $history = [];
                        $history['order_status_id'] = $this->config->get('payment_securetrading_pp_refunded_order_status_id');
                        $history['comment'] = '';
                        $history['notify'] = '';

                        $this->model_sale_order->addOrderHistory($this->request->post['order_id'], $history);
                    } else {
                        $rebate_status = 0;

                        $json['msg'] = $this->language->get('text_rebate_ok');
                    }

                    $json['data'] = [];
                    $json['data']['created'] = date('Y-m-d H:i:s');
                    $json['data']['amount'] = $amount * -1;
                    $json['data']['total_released'] = (double)$total_released;
                    $json['data']['total_rebated'] = (double)$total_rebated;
                    $json['data']['rebate_status'] = $rebate_status;

                    $json['error'] = false;
                } else {
                    $json['msg'] = (string)$response_xml->response->error->message;

                    $json['error'] = true;
                }
            } else {
                $json['status'] = 0;

                $json['message'] = $this->language->get('error_connection');
            }
        } else {
            $json['msg'] = $this->language->get('error_data_missing');

            $json['error'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/securetrading_pp')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_securetrading_pp_site_reference']) {
            $this->error['site_reference'] = $this->language->get('error_site_reference');
        }

        if (!$this->request->post['payment_securetrading_pp_version']) {
            $this->error['version'] = $this->language->get('error_version');
        }

        if (empty($this->request->post['payment_securetrading_pp_cards_accepted'])) {
            $this->error['cards_accepted'] = $this->language->get('error_cards_accepted');
        }

        if (!$this->request->post['payment_securetrading_pp_notification_password']) {
            $this->error['notification_password'] = $this->language->get('error_notification_password');
        }

        return !$this->error;
    }
}