<?php
class ModelExtensionShippingUps extends Model {
    public function getQuote(array $address): array {
        $this->load->language('extension/shipping/ups');

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('shipping_ups_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

        if (!$this->config->get('shipping_ups_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = [];

        if ($status) {
            $weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('shipping_ups_weight_class_id'));
            $weight_code = strtoupper($this->weight->getUnit($this->config->get('shipping_ups_weight_class_id')));

            if ($weight_code == 'KG') {
                $weight_code = 'KGS';
            } elseif ($weight_code == 'LB') {
                $weight_code = 'LBS';
            }

            $weight = ($weight < 0.1 ? 0.1 : $weight);
            $length = $this->length->convert($this->config->get('shipping_ups_length'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
            $width = $this->length->convert($this->config->get('shipping_ups_width'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
            $height = $this->length->convert($this->config->get('shipping_ups_height'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
            $length_code = strtoupper($this->length->getUnit($this->config->get('shipping_ups_length_class_id')));

            $service_code = [];

            $service_code = [
                // US Origin
                'US'    => [
                    '01' => $this->language->get('text_us_origin_01'),
                    '02' => $this->language->get('text_us_origin_02'),
                    '03' => $this->language->get('text_us_origin_03'),
                    '07' => $this->language->get('text_us_origin_07'),
                    '08' => $this->language->get('text_us_origin_08'),
                    '11' => $this->language->get('text_us_origin_11'),
                    '12' => $this->language->get('text_us_origin_12'),
                    '13' => $this->language->get('text_us_origin_13'),
                    '14' => $this->language->get('text_us_origin_14'),
                    '54' => $this->language->get('text_us_origin_54'),
                    '59' => $this->language->get('text_us_origin_59'),
                    '65' => $this->language->get('text_us_origin_65')
                ],
                // Canada Origin
                'CA'    => [
                    '01' => $this->language->get('text_ca_origin_01'),
                    '02' => $this->language->get('text_ca_origin_02'),
                    '07' => $this->language->get('text_ca_origin_07'),
                    '08' => $this->language->get('text_ca_origin_08'),
                    '11' => $this->language->get('text_ca_origin_11'),
                    '12' => $this->language->get('text_ca_origin_12'),
                    '13' => $this->language->get('text_ca_origin_13'),
                    '14' => $this->language->get('text_ca_origin_14'),
                    '54' => $this->language->get('text_ca_origin_54'),
                    '65' => $this->language->get('text_ca_origin_65')
                ],
                // European Union Origin
                'EU'    => [
                    '07' => $this->language->get('text_eu_origin_07'),
                    '08' => $this->language->get('text_eu_origin_08'),
                    '11' => $this->language->get('text_eu_origin_11'),
                    '54' => $this->language->get('text_eu_origin_54'),
                    '65' => $this->language->get('text_eu_origin_65'),
                    // next five services Poland domestic only
                    '82' => $this->language->get('text_eu_origin_82'),
                    '83' => $this->language->get('text_eu_origin_83'),
                    '84' => $this->language->get('text_eu_origin_84'),
                    '85' => $this->language->get('text_eu_origin_85'),
                    '86' => $this->language->get('text_eu_origin_86')
                ],
                // Puerto Rico Origin
                'PR'    => [
                    '01' => $this->language->get('text_pr_origin_01'),
                    '02' => $this->language->get('text_pr_origin_02'),
                    '03' => $this->language->get('text_pr_origin_03'),
                    '07' => $this->language->get('text_pr_origin_07'),
                    '08' => $this->language->get('text_pr_origin_08'),
                    '14' => $this->language->get('text_pr_origin_14'),
                    '54' => $this->language->get('text_pr_origin_54'),
                    '65' => $this->language->get('text_pr_origin_65')
                ],
                // Mexico Origin
                'MX'    => [
                    '07' => $this->language->get('text_mx_origin_07'),
                    '08' => $this->language->get('text_mx_origin_08'),
                    '54' => $this->language->get('text_mx_origin_54'),
                    '65' => $this->language->get('text_mx_origin_65')
                ],
                // All other origins
                'other' => [
                    // service code 7 seems to be gone after January 2, 2007
                    '07' => $this->language->get('text_other_origin_07'),
                    '08' => $this->language->get('text_other_origin_08'),
                    '11' => $this->language->get('text_other_origin_11'),
                    '54' => $this->language->get('text_other_origin_54'),
                    '65' => $this->language->get('text_other_origin_65')
                ]
            ];

            $xml = '<?xml version="1.0"?>';
            $xml .= '<AccessRequest xml:lang="en-US">';
            $xml .= '	<AccessLicenseNumber>' . $this->config->get('shipping_ups_key') . '</AccessLicenseNumber>';
            $xml .= '	<UserId>' . $this->config->get('shipping_ups_username') . '</UserId>';
            $xml .= '	<Password>' . $this->config->get('shipping_ups_password') . '</Password>';
            $xml .= '</AccessRequest>';
            $xml .= '<?xml version="1.0"?>';
            $xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
            $xml .= '	<Request>';
            $xml .= '		<TransactionReference>';
            $xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
            $xml .= '			<XpciVersion>1.0001</XpciVersion>';
            $xml .= '		</TransactionReference>';
            $xml .= '		<RequestAction>Rate</RequestAction>';
            $xml .= '		<RequestOption>shop</RequestOption>';
            $xml .= '	</Request>';
            $xml .= '   <PickupType>';
            $xml .= '       <Code>' . $this->config->get('shipping_ups_pickup') . '</Code>';
            $xml .= '   </PickupType>';

            if ($this->config->get('shipping_ups_country') == 'US' && $this->config->get('shipping_ups_pickup') == '11') {
                $xml .= '   <CustomerClassification>';
                $xml .= '       <Code>' . $this->config->get('shipping_ups_classification') . '</Code>';
                $xml .= '   </CustomerClassification>';
            }

            $xml .= '	<Shipment>';
            $xml .= '		<Shipper>';
            $xml .= '			<Address>';
            $xml .= '				<City>' . $this->config->get('shipping_ups_city') . '</City>';
            $xml .= '				<StateProvinceCode>' . $this->config->get('shipping_ups_state') . '</StateProvinceCode>';
            $xml .= '				<CountryCode>' . $this->config->get('shipping_ups_country') . '</CountryCode>';
            $xml .= '				<PostalCode>' . $this->config->get('shipping_ups_postcode') . '</PostalCode>';
            $xml .= '			</Address>';
            $xml .= '		</Shipper>';
            $xml .= '		<ShipTo>';
            $xml .= '			<Address>';
            $xml .= ' 				<City>' . $address['city'] . '</City>';
            $xml .= '				<StateProvinceCode>' . $address['zone_code'] . '</StateProvinceCode>';
            $xml .= '				<CountryCode>' . $address['iso_code_2'] . '</CountryCode>';
            $xml .= '				<PostalCode>' . $address['postcode'] . '</PostalCode>';

            if ($this->config->get('shipping_ups_quote_type') == 'residential') {
                $xml .= '				<ResidentialAddressIndicator />';
            }

            $xml .= '			</Address>';
            $xml .= '		</ShipTo>';
            $xml .= '		<ShipFrom>';
            $xml .= '			<Address>';
            $xml .= '				<City>' . $this->config->get('shipping_ups_city') . '</City>';
            $xml .= '				<StateProvinceCode>' . $this->config->get('shipping_ups_state') . '</StateProvinceCode>';
            $xml .= '				<CountryCode>' . $this->config->get('shipping_ups_country') . '</CountryCode>';
            $xml .= '				<PostalCode>' . $this->config->get('shipping_ups_postcode') . '</PostalCode>';
            $xml .= '			</Address>';
            $xml .= '		</ShipFrom>';

            $xml .= '		<Package>';
            $xml .= '			<PackagingType>';
            $xml .= '				<Code>' . $this->config->get('shipping_ups_packaging') . '</Code>';
            $xml .= '			</PackagingType>';

            $xml .= '		    <Dimensions>';
            $xml .= '				<UnitOfMeasurement>';
            $xml .= '					<Code>' . $length_code . '</Code>';
            $xml .= '				</UnitOfMeasurement>';
            $xml .= '				<Length>' . $length . '</Length>';
            $xml .= '				<Width>' . $width . '</Width>';
            $xml .= '				<Height>' . $height . '</Height>';
            $xml .= '			</Dimensions>';

            $xml .= '			<PackageWeight>';
            $xml .= '				<UnitOfMeasurement>';
            $xml .= '					<Code>' . $weight_code . '</Code>';
            $xml .= '				</UnitOfMeasurement>';
            $xml .= '				<Weight>' . $weight . '</Weight>';
            $xml .= '			</PackageWeight>';

            if ($this->config->get('shipping_ups_insurance')) {
                $xml .= '           <PackageServiceOptions>';
                $xml .= '               <InsuredValue>';
                $xml .= '                   <CurrencyCode>' . $this->session->data['currency'] . '</CurrencyCode>';
                $xml .= '                   <MonetaryValue>' . $this->currency->format($this->cart->getSubTotal(), $this->session->data['currency'], false, false) . '</MonetaryValue>';
                $xml .= '               </InsuredValue>';
                $xml .= '           </PackageServiceOptions>';
            }

            $xml .= '		</Package>';
            $xml .= '	</Shipment>';
            $xml .= '</RatingServiceSelectionRequest>';

            if (!$this->config->get('shipping_ups_test')) {
                $url = 'https://onlinetools.ups.com/ups.app/xml/Rate';
            } else {
                $url = 'https://wwwcie.ups.com/ups.app/xml/Rate';
            }

            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

            $result = curl_exec($curl);

            curl_close($curl);

            $error = '';
            $quote_data = [];

            if ($result) {
                if ($this->config->get('shipping_ups_debug')) {
                    $this->log->write("UPS DATA SENT: " . $xml);
                    $this->log->write("UPS DATA RECV: " . $result);
                }

                $previous_value = libxml_use_internal_errors(true);

                $dom = new \DOMDocument('1.0', 'UTF-8');
                $dom->loadXml($result);

                libxml_use_internal_errors($previous_value);

                if (libxml_get_errors()) {
                    return false;
                }

                $rating_service_selection_response = $dom->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
                $response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);
                $response_status_code = $response->getElementsByTagName('ResponseStatusCode');

                if ($response_status_code->item(0)->nodeValue != '1') {
                    $error = $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorCode')->item(0)->nodeValue . ': ' . $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;
                } else {
                    $rated_shipments = $rating_service_selection_response->getElementsByTagName('RatedShipment');

                    foreach ($rated_shipments as $rated_shipment) {
                        $service = $rated_shipment->getElementsByTagName('Service')->item(0);
                        $code = $service->getElementsByTagName('Code')->item(0)->nodeValue;
                        $total_charges = $rated_shipment->getElementsByTagName('TotalCharges')->item(0);
                        $cost = $total_charges->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;
                        $currency = $total_charges->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;

                        if (!($code && $cost)) {
                            continue;
                        }

                        if ($this->config->get('shipping_ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code)) {
                            $quote_data[$code] = [
                                'code'         => 'ups.' . $code,
                                'title'        => $service_code[$this->config->get('shipping_ups_origin')][$code],
                                'cost'         => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
                                'tax_class_id' => $this->config->get('shipping_ups_tax_class_id'),
                                'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->session->data['currency']), $this->config->get('shipping_ups_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000)
                            ];
                        }
                    }
                }
            }

            $title = $this->language->get('text_title');

            if ($this->config->get('shipping_ups_display_weight')) {
                $title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('shipping_ups_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')) . ')';
            }

            if ($quote_data || $error) {
                $method_data = [
                    'code'       => 'ups',
                    'title'      => $title,
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('shipping_ups_sort_order'),
                    'error'      => $error
                ];
            }
        }

        return $method_data;
    }
}