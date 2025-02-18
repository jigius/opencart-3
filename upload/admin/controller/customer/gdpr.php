<?php
class ControllerCustomerGdpr extends Controller {
    public function index(): void {
        $this->load->language('customer/gdpr');

        $this->document->setTitle($this->language->get('heading_title'));

        // GDPR
        $this->load->model('customer/gdpr');

        $this->getList();
    }

    public function getList(): void {
        if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = '';
        }

        if (isset($this->request->get['filter_action'])) {
            $filter_action = $this->request->get['filter_action'];
        } else {
            $filter_action = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['filter_date_from'])) {
            $filter_date_from = $this->request->get['filter_date_from'];
        } else {
            $filter_date_from = '';
        }

        if (isset($this->request->get['filter_date_to'])) {
            $filter_date_to = $this->request->get['filter_date_to'];
        } else {
            $filter_date_to = '';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_email'])) {
            $url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_action'])) {
            $url .= '&filter_action=' . $this->request->get['filter_action'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_date_from'])) {
            $url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
        }

        if (isset($this->request->get['filter_date_to'])) {
            $url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/gdpr', 'user_token=' . $this->session->data['user_token'], true)
        ];

        // GDPR
        $data['gdprs'] = [];

        $filter_data = [
            'filter_email'     => $filter_email,
            'filter_action'    => $filter_action,
            'filter_status'    => $filter_status,
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to,
            'start'            => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'            => $this->config->get('config_limit_admin')
        ];

        // Customers
        $this->load->model('customer/customer');

        $gdpr_total = $this->model_customer_gdpr->getTotalGdprs($filter_data);

        $results = $this->model_customer_gdpr->getGdprs($filter_data);

        foreach ($results as $result) {
            $customer_info = $this->model_customer_customer->getCustomerByEmail($result['email']);

            if ($customer_info) {
                $edit = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_info['customer_id'], true);
            } else {
                $edit = '';
            }

            $data['gdprs'][] = [
                'gdpr_id'    => $result['gdpr_id'],
                'email'      => $result['email'],
                'action'     => $this->language->get('text_' . $result['action']),
                'status'     => $result['status'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'approve'    => $this->url->link('customer/gdpr/approve', 'user_token=' . $this->session->data['user_token'] . '&gdpr_id=' . $result['gdpr_id'], true),
                'deny'       => $this->url->link('customer/gdpr/deny', 'user_token=' . $this->session->data['user_token'] . '&gdpr_id=' . $result['gdpr_id'], true),
                'edit'       => $edit,
                'delete'     => $this->url->link('customer/gdpr/delete', 'user_token=' . $this->session->data['user_token'] . '&gdpr_id=' . $result['gdpr_id'], true)
            ];
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['filter_email'] = $filter_email;
        $data['filter_action'] = $filter_action;
        $data['filter_status'] = $filter_status;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to'] = $filter_date_to;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/gdpr', $data));
    }

    /*
     *  Action Statuses
     *
     *	EXPORT
     *
     *  unverified = 0
     *	pending    = 1
     *	complete   = 3
     *
     *	REMOVE
     *
     *  unverified = 0
     *	pending    = 1
     *	processing = 2
     *	delete     = 3
     *
     *	DENY
     *
     *  unverified = 0
     *	pending    = 1
     *	processing = 2
     *	denied     = -1
    */
    public function approve(): void {
        $this->load->language('customer/gdpr');

        $json = [];

        if (!$this->user->hasPermission('modify', 'customer/gdpr')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $gdprs = [];

            if (isset($this->request->post['selected'])) {
                $gdprs = $this->request->post['selected'];
            }

            if (isset($this->request->get['gdpr_id'])) {
                $gdprs[] = (int)$this->request->get['gdpr_id'];
            }

            // GDPR
            $this->load->model('customer/gdpr');

            foreach ($gdprs as $gdpr_id) {
                $gdpr_info = $this->model_customer_gdpr->getGdpr($gdpr_id);

                if ($gdpr_info) {
                    // If we remove we want to change the status to processing
                    // to give time for store owners to process orders and refunds.
                    if ($gdpr_info['action'] == 'export') {
                        $this->model_customer_gdpr->editStatus($gdpr_id, 3);
                    } else {
                        $this->model_customer_gdpr->editStatus($gdpr_id, 2);
                    }
                }
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deny(): void {
        $this->load->language('customer/gdpr');

        $json = [];

        if (!$this->user->hasPermission('modify', 'customer/gdpr')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $gdprs = [];

            if (isset($this->request->post['selected'])) {
                $gdprs = $this->request->post['selected'];
            }

            if (isset($this->request->get['gdpr_id'])) {
                $gdprs[] = (int)$this->request->get['gdpr_id'];
            }

            // GDPR
            $this->load->model('customer/gdpr');

            foreach ($gdprs as $gdpr_id) {
                $this->model_customer_gdpr->editStatus($gdpr_id, -1);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete(): void {
        $this->load->language('customer/gdpr');

        $json = [];

        if (!$this->user->hasPermission('modify', 'customer/gdpr')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $gdprs = [];

            if (isset($this->request->post['selected'])) {
                $gdprs = $this->request->post['selected'];
            }

            if (isset($this->request->get['gdpr_id'])) {
                $gdprs[] = (int)$this->request->get['gdpr_id'];
            }

            // GDPR
            $this->load->model('customer/gdpr');

            foreach ($gdprs as $gdpr_id) {
                $this->model_customer_gdpr->deleteGdpr($gdpr_id);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}