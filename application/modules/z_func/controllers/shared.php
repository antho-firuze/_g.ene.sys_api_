<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shared extends CI_Controller {

	function __construct() {
		parent::__construct();
		
	}

	function pull(){
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
		
		// MCHAT AUTOMATION
		$this->mchat_lib->automation();
		
		// TICKET AUTOMATION
		$this->ticketing_lib->automation();
		
		// CHECK DATA ON COMET TABLE
		$data = get_comet();
		$data['connected'] = TRUE;
		
		$json = json_encode($data);
		echo "data: $json \n\n";
		flush();
	}
	
	function push_notification(){
		$this->shared_model->push_notification_email();
	}
	
	function cronjob_update_table_master() {
		$this->db->trans_begin();
		$qry_customer = "SELECT case [kodecomp] \n".
			"when 'FBI' then 1 \n".
			"when 'TGS' then 2 \n".
			"when 'JFI' then 3 \n".
			"end as company_id\n".
			",case [kodecabang] \n".
			"when 'FB00' then 1 \n".
			"when 'FB01' then 2 \n".
			"when 'FB02' then 3 \n".
			"when 'FB03' then 4 \n".
			"when 'FB04' then 7 \n".
			"when 'FB05' then 9 \n".
			"when 'FB06' then 13 \n".
			"when 'FB07' then 12 \n".
			"when 'FB08' then 11 \n".
			"when 'FB09' then 5 \n".
			"when 'FB10' then 10 \n".
			"when 'FB11' then 8 \n".
			"when 'FB12' then 6 \n".
			"when 'FB13' then 14 \n".
			"when 'FB14' then 15 \n".
			"when 'FB15' then 16 \n".
			"when 'FB16' then 19 \n".
			"when 'FB17' then 20 \n".
			"when 'FB18' then 22 \n".
			"when 'TG01' then 1 \n".
			"when 'TG02' then 2 \n".
			"when 'TG03' then 4 \n".
			"when 'TG04' then 12 \n".
			"when 'TG05' then 18 \n".
			"when 'TG06' then 21 \n".
			"when 'JF01' then 1 \n".
			"when 'JF02' then 21 \n".
			"end as branch_id\n".
			",[kodecustomer]\n".
			",[namacustomer]\n".
			",[alamat]\n".
			",[telepon]\n".
			",P.[fax]\n".
			",[contactperson]\n".
			",[npwp]\n".
			"INTO [db_genesys].[dbo].tmp_customer \n".
			"FROM PURCHASING.[dbo].[PCUSTOMER] P\n".
			"WHERE [kodecabang] in ('FB00','FB01','FB02','FB03','FB04','FB05','FB06','FB07','FB08','FB09','FB10','FB11','FB12','FB13','FB14','FB15','FB16','FB17','FB18','TG01','TG02','TG03','TG04','TG05','TG06','JF01','JF02') \n".
			"INSERT INTO [db_genesys].[dbo].[customer] (\n".
			"[company_id]\n".
			",[branch_id]\n".
			",[code]\n".
			",[name]\n".
			",[address]\n".
			",[phone1]\n".
			",[fax]\n".
			",[contact_person]\n".
			",[npwp])\n".
			"SELECT company_id\n".
			",branch_id\n".
			",[kodecustomer]\n".
			",[namacustomer]\n".
			",[alamat]\n".
			",[telepon]\n".
			",P.[fax]\n".
			",[contactperson]\n".
			",[npwp]\n".
			"FROM [db_genesys].[dbo].tmp_customer P\n".
			"WHERE NOT EXISTS \n".
			"(\n".
			"SELECT * \n".
			"FROM [db_genesys].[dbo].[customer]\n".
			"WHERE \n".
			"code = P.[kodecustomer] AND\n".
			"company_id = P.company_id AND\n".
			"branch_id = P.branch_id\n".
			")\n".
			"DROP TABLE [db_genesys].[dbo].tmp_customer";
		$qry = $this->db->query( $qry_customer );
		
		$qry_salesman = "SELECT case [kodecomp] \n".
			"when 'FBI' then 1 \n".
			"when 'TGS' then 2 \n".
			"when 'JFI' then 3 \n".
			"end as company_id\n".
			",case [kodecabang] \n".
			"when 'FB00' then 1 \n".
			"when 'FB01' then 2 \n".
			"when 'FB02' then 3 \n".
			"when 'FB03' then 4 \n".
			"when 'FB04' then 7 \n".
			"when 'FB05' then 9 \n".
			"when 'FB06' then 13 \n".
			"when 'FB07' then 12 \n".
			"when 'FB08' then 11 \n".
			"when 'FB09' then 5 \n".
			"when 'FB10' then 10 \n".
			"when 'FB11' then 8 \n".
			"when 'FB12' then 6 \n".
			"when 'FB13' then 14 \n".
			"when 'FB14' then 15 \n".
			"when 'FB15' then 16 \n".
			"when 'FB16' then 19 \n".
			"when 'FB17' then 20 \n".
			"when 'FB18' then 22 \n".
			"when 'TG01' then 1 \n".
			"when 'TG02' then 2 \n".
			"when 'TG03' then 4 \n".
			"when 'TG04' then 12 \n".
			"when 'TG05' then 18 \n".
			"when 'TG06' then 21 \n".
			"when 'JF01' then 1 \n".
			"when 'JF02' then 21 \n".
			"end as branch_id\n".
			",[kodesalesman]\n".
			",[namasalesman]\n".
			"INTO [db_genesys].[dbo].[tmp_salesman]\n".
			"FROM PURCHASING.[dbo].[PSALESMAN]\n".
			"WHERE [kodecabang] in ('FB00','FB01','FB02','FB03','FB04','FB05','FB06','FB07','FB08','FB09','FB10','FB11','FB12','FB13','FB14','FB15','FB16','FB17','FB18','TG01','TG02','TG03','TG04','TG05','TG06','JF01','JF02') \n".
			"INSERT INTO [db_genesys].[dbo].[salesman] (\n".
			"[company_id]\n".
			",[branch_id]\n".
			",[code]\n".
			",[name])\n".
			"SELECT company_id\n".
			",branch_id\n".
			",[kodesalesman]\n".
			",[namasalesman]\n".
			"FROM [db_genesys].[dbo].[tmp_salesman] P\n".
			"WHERE NOT EXISTS \n".
			"(\n".
			"SELECT code \n".
			"FROM [db_genesys].[dbo].[salesman]\n".
			"WHERE code = P.[kodesalesman] AND\n".
			"company_id = P.company_id AND\n".
			"branch_id = P.branch_id\n".
			")\n".
			"DROP TABLE [db_genesys].[dbo].[tmp_salesman]";
		$qry = $this->db->query( $qry_salesman );
		
		$qry_supplier = "SELECT case [kodecomp] \n".
			"when 'FBI' then 1 \n".
			"when 'TGS' then 2 \n".
			"when 'JFI' then 3 \n".
			"end as company_id\n".
			",case [kodecabang] \n".
			"when 'FB00' then 1 \n".
			"when 'FB01' then 2 \n".
			"when 'FB02' then 3 \n".
			"when 'FB03' then 4 \n".
			"when 'FB04' then 7 \n".
			"when 'FB05' then 9 \n".
			"when 'FB06' then 13 \n".
			"when 'FB07' then 12 \n".
			"when 'FB08' then 11 \n".
			"when 'FB09' then 5 \n".
			"when 'FB10' then 10 \n".
			"when 'FB11' then 8 \n".
			"when 'FB12' then 6 \n".
			"when 'FB13' then 14 \n".
			"when 'FB14' then 15 \n".
			"when 'FB15' then 16 \n".
			"when 'FB16' then 19 \n".
			"when 'FB17' then 20 \n".
			"when 'FB18' then 22 \n".
			"when 'TG01' then 1 \n".
			"when 'TG02' then 2 \n".
			"when 'TG03' then 4 \n".
			"when 'TG04' then 12 \n".
			"when 'TG05' then 18 \n".
			"when 'TG06' then 21 \n".
			"when 'JF01' then 1 \n".
			"when 'JF02' then 21 \n".
			"end as branch_id,[kodesupplier]\n".
			",[namasupplier]\n".
			",[alamat]\n".
			",[telepon]\n".
			",[fax]\n".
			",[contactperson],[email]\n".
			"INTO [db_genesys].[dbo].[tmp_suppliers]\n".
			"FROM PURCHASING.[dbo].[PSUPPLIER]\n".
			"WHERE [kodecabang] in ('FB00','FB01','FB02','FB03','FB04','FB05','FB06','FB07','FB08','FB09','FB10','FB11','FB12','FB13','FB14','FB15','FB16','FB17','FB18','TG01','TG02','TG03','TG04','TG05','TG06','JF01','JF02') \n".
			"INSERT INTO [db_genesys].[dbo].[suppliers] (\n".
			"[company_id]\n".
			",[branch_id]\n".
			",[code]\n".
			",[name]\n".
			",[address],[phone],[fax],[contactperson],[email])\n".
			"SELECT company_id\n".
			",branch_id,[kodesupplier]\n".
			",[namasupplier]\n".
			",[alamat]\n".
			",[telepon]\n".
			",[fax]\n".
			",[contactperson]\n".
			",[email]\n".
			"FROM [db_genesys].[dbo].[tmp_suppliers] P\n".
			"WHERE NOT EXISTS \n".
			"(\n".
			"SELECT code \n".
			"FROM [db_genesys].[dbo].[suppliers]\n".
			"WHERE code = P.[kodesupplier] AND\n".
			"company_id = P.company_id AND\n".
			"branch_id = P.branch_id\n".
			")\n".
			"DROP TABLE [db_genesys].[dbo].[tmp_suppliers]";
		$qry = $this->db->query( $qry_supplier );
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
			// echo "Error: Update table master failed !";
		}
		else
		{
			$this->db->trans_commit();
			echo "Success !";
			return TRUE;
		}
	}
	
	function cronjob_update_table_master2() {
		$this->db->trans_begin();
		$qry_customer = "SELECT 
			case [kodecomp] when 'FBI' then 1 when 'TGS' then 2 when 'JFI' then 3 end as company_id
			,case [kodecabang] 
			when 'FB00' then 1 
			when 'FB01' then 2 
			when 'FB02' then 3 
			when 'FB03' then 4 
			when 'FB04' then 7 
			when 'FB05' then 9 
			when 'FB06' then 13 
			when 'FB07' then 12 
			when 'FB08' then 11 
			when 'FB09' then 5 
			when 'FB10' then 10 
			when 'FB11' then 8 
			when 'FB12' then 6 
			when 'FB13' then 14 
			when 'FB14' then 15 
			when 'FB15' then 16 
			when 'FB16' then 19 
			when 'FB17' then 20 
			when 'FB18' then 22 
			when 'TG01' then 1 
			when 'TG02' then 2 
			when 'TG03' then 4 
			when 'TG04' then 12 
			when 'TG05' then 18 
			when 'TG06' then 21 
			when 'JF01' then 1 
			when 'JF02' then 21 
			end as branch_id
			,[kodecustomer],[namacustomer],[alamat],[telepon],P.[fax],[contactperson],[npwp]
			INTO [db_genesys].[dbo].tmp_customer 
			FROM PURCHASING.[dbo].[PCUSTOMER] P
			WHERE [kodecabang] in ('FB00','FB01','FB02','FB03','FB04','FB05','FB06','FB07','FB08','FB09','FB10','FB11','FB12','FB13','FB14','FB15','FB16','FB17','FB18','TG01','TG02','TG03','TG04','TG05','TG06','JF01','JF02') 
			INSERT INTO [db_genesys].[dbo].[customer] ([company_id],[branch_id],[code],[name],[address],[phone1],[fax],[contact_person],[npwp])
			SELECT company_id,branch_id,[kodecustomer],[namacustomer],[alamat],[telepon],P.[fax],[contactperson],[npwp]
			FROM [db_genesys].[dbo].tmp_customer P
			WHERE NOT EXISTS 
			(
			SELECT * 
			FROM [db_genesys].[dbo].[customer]
			WHERE 
			code = P.[kodecustomer] AND
			company_id = P.company_id AND
			branch_id = P.branch_id
			)
			DROP TABLE [db_genesys].[dbo].tmp_customer";
		$qry = $this->db->query( $qry_customer );
		
		$qry_salesman = "SELECT 
			case [kodecomp] when 'FBI' then 1 when 'TGS' then 2 when 'JFI' then 3 end as company_id
			,case [kodecabang] 
			when 'FB00' then 1 
			when 'FB01' then 2 
			when 'FB02' then 3 
			when 'FB03' then 4 
			when 'FB04' then 7 
			when 'FB05' then 9 
			when 'FB06' then 13 
			when 'FB07' then 12 
			when 'FB08' then 11 
			when 'FB09' then 5 
			when 'FB10' then 10 
			when 'FB11' then 8 
			when 'FB12' then 6 
			when 'FB13' then 14 
			when 'FB14' then 15 
			when 'FB15' then 16 
			when 'FB16' then 19 
			when 'FB17' then 20 
			when 'FB18' then 22 
			when 'TG01' then 1 
			when 'TG02' then 2 
			when 'TG03' then 4 
			when 'TG04' then 12 
			when 'TG05' then 18 
			when 'TG06' then 21 
			when 'JF01' then 1 
			when 'JF02' then 21 
			end as branch_id
			,[kodesalesman]
			,[namasalesman]
			INTO [db_genesys].[dbo].[tmp_salesman]
			FROM PURCHASING.[dbo].[PSALESMAN]
			WHERE [kodecabang] in ('FB00','FB01','FB02','FB03','FB04','FB05','FB06','FB07','FB08','FB09','FB10','FB11','FB12','FB13','FB14','FB15','FB16','FB17','FB18','TG01','TG02','TG03','TG04','TG05','TG06','JF01','JF02') 
			INSERT INTO [db_genesys].[dbo].[salesman] ([company_id],[branch_id],[code],[name])
			SELECT company_id,branch_id,[kodesalesman],[namasalesman]
			FROM [db_genesys].[dbo].[tmp_salesman] P
			WHERE NOT EXISTS 
			(
			SELECT code 
			FROM [db_genesys].[dbo].[salesman]
			WHERE code = P.[kodesalesman] AND
			company_id = P.company_id AND
			branch_id = P.branch_id
			)
			DROP TABLE [db_genesys].[dbo].[tmp_salesman]";
		$qry = $this->db->query( $qry_salesman );
		
		$qry_supplier = "SELECT 
			case [kodecomp] when 'FBI' then 1 when 'TGS' then 2 when 'JFI' then 3 end as company_id
			,case [kodecabang] 
			when 'FB00' then 1 
			when 'FB01' then 2 
			when 'FB02' then 3 
			when 'FB03' then 4 
			when 'FB04' then 7 
			when 'FB05' then 9 
			when 'FB06' then 13 
			when 'FB07' then 12 
			when 'FB08' then 11 
			when 'FB09' then 5 
			when 'FB10' then 10 
			when 'FB11' then 8 
			when 'FB12' then 6 
			when 'FB13' then 14 
			when 'FB14' then 15 
			when 'FB15' then 16 
			when 'FB16' then 19 
			when 'FB17' then 20 
			when 'FB18' then 22 
			when 'TG01' then 1 
			when 'TG02' then 2 
			when 'TG03' then 4 
			when 'TG04' then 12 
			when 'TG05' then 18 
			when 'TG06' then 21 
			when 'JF01' then 1 
			when 'JF02' then 21 
			end as branch_id,[kodesupplier]
			,[namasupplier]
			,[alamat]
			,[telepon]
			,[fax]
			,[contactperson],[email]
			INTO [db_genesys].[dbo].[tmp_suppliers]
			FROM PURCHASING.[dbo].[PSUPPLIER]
			WHERE [kodecabang] in ('FB00','FB01','FB02','FB03','FB04','FB05','FB06','FB07','FB08','FB09','FB10','FB11','FB12','FB13','FB14','FB15','FB16','FB17','FB18','TG01','TG02','TG03','TG04','TG05','TG06','JF01','JF02') 
			INSERT INTO [db_genesys].[dbo].[suppliers] ([company_id],[branch_id],[code],[name],[address],[phone],[fax],[contactperson],[email])
			SELECT company_id,branch_id,[kodesupplier],[namasupplier],[alamat],[telepon],[fax],[contactperson],[email]
			FROM [db_genesys].[dbo].[tmp_suppliers] P
			WHERE NOT EXISTS 
			(
			SELECT code 
			FROM [db_genesys].[dbo].[suppliers]
			WHERE code = P.[kodesupplier] AND
			company_id = P.company_id AND
			branch_id = P.branch_id
			)
			DROP TABLE [db_genesys].[dbo].[tmp_suppliers]";
		$qry = $this->db->query( $qry_supplier );
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
			// echo "Error: Update table master failed !";
		}
		else
		{
			$this->db->trans_commit();
			echo "Success !";
			return TRUE;
		}
	}
	
	function comet_server() {
	
		// set_time_limit (600);
		// define("IDLE_TIME", 3); // 3 seconds idle
		
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

		$data = get_comet();
		$json = json_encode($data);
		if ( $json )
			echo "data: $json \n\n";
		else
			echo "data: \n\n";
		flush();

		/* do {
			sleep(IDLE_TIME);
			$result['note'] = $this->shared_model->get_notif_note();
			echo "data: KOSONG "."\n\n";
		} while ( empty($result['note']) );
		
		echo "data: " . json_encode($result) . "\n\n"; */

		/* while (true) {
			$result['note'] = $this->shared_model->get_notif_note();
			
			if ( $result['note'] )
				echo "data: " . json_encode($result) . "\n\n";
			else
				echo "data: KOSONG "."\n\n";
			// echo "data: " . json_encode($result) . "\n\n";
			// printf ('data: {"note" : "%s"}' . "\n\n", $this->shared_model->get_notif_note());
			
			// ob_flush();
			flush();
			
			sleep(IDLE_TIME);
		}
		gc_collect_cycles(); */


		// $str_filter = '@xyz@';
		// $check_memcache = @memcache_connect('127.0.0.1',11211);
		// if( $check_memcache !== FALSE ){
			// $result = $this->cache->memcached->get( $str_filter );
			// if ( $result ) { $this->cache->memcached->delete( $str_filter ); } 
		// }
			
/* 		do {
			sleep(IDLE_TIME);
			$result = $this->cache->memcached->get( $str_filter );
		} while ( empty($result) );
		
		
		$this->cache->memcached->delete( $str_filter ); 

		header("HTTP/1.0 200");
		echo json_encode(array("result"=>$result));

		// Clean up memory and stuff like that.
		flush();
		gc_collect_cycles(); */
	}

	function upload($filepath=NULL){
		$data = $this->input->post();
		if ( empty($data) ) 
			crud_error("Error: Empty Data !");
		
		$this->load->library('Plupload');
		$oPlupload = new PluploadHandler();
		$oPlupload->no_cache_headers();
		$oPlupload->cors_headers();
		
		$config['target_dir'] 	 	= (empty($filepath) ? "./tmp/" : $filepath);
		$config['allow_extensions'] = 'jpg,jpeg,png';
		if ( !$oPlupload->handle($config) )
			crud_error( $oPlupload->get_error_code().": ".$oPlupload->get_error_message() );
		
		crud_success( array('OK' => 1) );
	}
	
}