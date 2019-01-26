<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Scheduler Class
 *
 * This class contains functions for scheduling
 *
 */
class Scheduler extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
        $this->load->library('f');
        $this->load->helper('logger');
	}
    
    function logger($message='NONAME')
    {
        if (PHP_OS === 'WINNT')
            logme('scheduler', 'info', "Method [$message]");
    }

    function mail_send()
    {
        $this->db->where('is_test','0');
        $this->db->where('trying <=','3');
        $this->db->where('is_sent','0');
        $this->db->where_in('status',['waiting','failed']);
        $this->db->order_by('queueID');
		$row = $this->db->get('mobc_mail_queue')->row();
		if (!$row) {
            $this->logger(__FUNCTION__);
            die('No email queue');
        }
        
        $this->db->update('mobc_mail_queue', ['status' => 'queuing'], ['queueID' => $row->queueID]);
        $email = (object)[
            'config'    => json_decode($row->_config),
            'from'      => $row->_from,
            'to'        => $row->_to,
            'cc'        => $row->_cc,
            'bcc'       => $row->_bcc,
            'subject'   => $row->_subject,
            'body'      => $row->_body,
            'attachment' => (array) json_decode($row->_attachment),
        ];
        // die(var_dump($email->config->smtp_host));

        $data = [
            'trying' => $row->trying + 1,
            'LastTryAt' => date('Y-m-d H:i:s'),
        ];
        list($success, $return) = $this->f->mail_send($email);
        if (!$success) {
            $data['LastErrorMsg'] = $return['message'];
            $data['is_sent'] = '0';
            $data['status'] = 'failed';
        } else {
            $data['LastErrorMsg'] = NULL;
            $data['is_sent'] = '1';
            $data['status'] = 'success';
        }

        $this->db->update('mobc_mail_queue', $data, ['queueID' => $row->queueID]);
        $this->logger(__FUNCTION__);
        die(($success ? 'success' : $return['message']));
    }

    function retrieve_log_api()
    {
        if (!$row = $this->db->get_where('simpi_log_api_tmp', NULL, 1)->row())
            return;
        
        if (! $row->is_local) {
            $ip = json_decode(file_get_contents('http://ip-api.com/json/'.$row->ip_address.'?fields=262143'));
            if ($ip->status == 'success') {
                $data = [
                    'isp'           => $ip->isp,
                    'org'           => $ip->org,
                    'country'       => $ip->country,
                    'country_code'  => $ip->countryCode,
                    'region'        => $ip->region,
                    'region_name'   => $ip->regionName,
                    'city'          => $ip->city,
                    'zip'           => $ip->zip,
                    'lat'           => $ip->lat,
                    'lon'           => $ip->lon,
                    'timezone'      => $ip->timezone,
                    'as_number'     => $ip->as,
                    'reverse'       => !empty($ip->reverse) ? $ip->reverse : NULL,
                    'mobile'        => $ip->mobile ? '1' : '0',
                ];
            } 
        }

        $request = json_decode($row->request);
        if (isset($request->licensekey)) {
            $this->f->is_valid_licensekey($request);
        } elseif (isset($request->appcode)) {
            $this->f->is_valid_appcode($request);
        } elseif (isset($request->token)) {
            $this->f->is_valid_token($request);
        }
        $data['simpiID'] = isset($request->simpiID) ? $request->simpiID : NULL;
        $data['AppsID'] = isset($request->AppsID) ? $request->AppsID : NULL;
        $data['ClientID'] = isset($request->ClientID) ? $request->ClientID : NULL;
        
        $data_insert = array_merge((array) $row, $data);
        // $this->f->debug($row);
        $this->db->insert('simpi_log_api', $data_insert);
        $this->db->delete('simpi_log_api_tmp', ['id' => $row->id]);
    }

    function test()
    {
        die($this->f->get_ip_address());
    }
}