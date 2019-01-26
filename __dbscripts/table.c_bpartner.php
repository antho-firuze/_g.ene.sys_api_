<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] = ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];
$fields['parent_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];					
$fields['client_id'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['org_id'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['dept_id'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['div_id'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['is_active'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '1'];
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['created_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['updated_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['updated_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];

/* Shared fields => is_prospect is used by Employee, Customer, Vendor & Agent */
$fields['is_prospect'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['is_individual'] = ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
/* Identification => Own Employee / Teacher */
$fields['is_employee'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
/* Identification => Customer / Student */
$fields['is_customer'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['is_salesrep'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
/* Identification => Vendor */
$fields['is_vendor'] 		= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['is_vendorrep'] = ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
/* Identification => Agent */
$fields['is_agent'] 		= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['is_agentrep'] = ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];

/* Own Employee area */
$fields['company_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['branch_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['dept_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['div_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['user_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['gender_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['religion_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['marital_status_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['marital_tax_status_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['education_level_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['home_status_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['job_title_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['nationality_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['occupation_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['employee_level_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['bank_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['bank_account_no'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['begin_date'] = 	['type' => 'DATE', 'null' => TRUE];
$fields['end_date'] = 	['type' => 'DATE', 'null' => TRUE];
$fields['employee_id'] 	= ['type' => 'VARCHAR', 'constraint' => '60',  'null' => TRUE];
$fields['employee_status_id'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['number_leave_status'] = ['type' => 'NUMERIC', 'constraint' => '10', 'null' => TRUE];
$fields['bpjs_tk_no'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['bpjs_kes_no'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['father_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['mother_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['spouse_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['child1_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['child2_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['child3_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['photo_file'] = ['type' => 'VARCHAR', 'constraint' => '120', 'null' => TRUE];
$fields['home_distance'] = ['type' => 'NUMERIC', 'constraint' => '18,2', 'null' => TRUE];
/* Personal/Individual area */
$fields['greeting_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['first_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['last_name'] 	= ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['birth_place'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];
$fields['birth_date'] = ['type' => 'DATE', 'null' => TRUE];
/* Non Individual area */
$fields['code'] = ['type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE];
$fields['name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => FALSE];
$fields['description'] = ['type' => 'TEXT', 'null' => TRUE];
/* For Student */
$fields['family_status_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['f_parent_type_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['m_parent_type_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['f_parent_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['f_parent_birth_place'] = 	['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['f_parent_birth_date'] = 	['type' => 'DATE', 'null' => TRUE];
$fields['f_parent_religion_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['f_parent_nationality_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['f_parent_occupation_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['f_parent_education_level_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['m_parent_name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['m_parent_birth_place'] = 	['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['m_parent_birth_date'] = 	['type' => 'DATE', 'null' => TRUE];
$fields['m_parent_religion_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['m_parent_nationality_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['m_parent_occupation_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['m_parent_education_level_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['child_to'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['sibling_number'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
/* Common area */
$fields['website'] 	= ['type' => 'VARCHAR', 'constraint' => '120', 'null' => TRUE];
$fields['email'] 	= ['type' => 'VARCHAR', 'constraint' => '120', 'null' => TRUE];
$fields['phone'] 	= ['type' => 'VARCHAR', 'constraint' => '120', 'null' => TRUE];
$fields['npwp_no'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['npwp_date'] 	= ['type' => 'DATE', 'null' => TRUE];
$fields['npwp_address'] 	= ['type' => 'TEXT', 'null' => TRUE];
/* Sales area */
$fields['is_sotaxexempt'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['salesrep_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['number_employees'] = ['type' => 'NUMERIC', 'constraint' => '10', 'null' => TRUE];
$fields['so_creditstatus'] = ['type' => 'CHAR', 'constraint' => '1', 'null' => TRUE];
$fields['so_creditlimit'] = ['type' => 'NUMERIC', 'constraint' => '0', 'null' => TRUE];
$fields['so_creditused'] = ['type' => 'NUMERIC', 'constraint' => '0', 'null' => TRUE];
$fields['so_pricelist_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['invoice_rule'] = 	['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['delivery_rule'] = 	['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['deliveryvia_rule'] = 	['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['freightcost_rule'] = 	['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
/* Purchasing area */
$fields['is_potaxexempt'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['po_pricelist_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
/* Finance area */
$fields['taxid'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['so_top'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE, 'default' => '0'];
$fields['po_top'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE, 'default' => '0'];
