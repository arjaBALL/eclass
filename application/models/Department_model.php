<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model
{
    public function validate_data($department, $statusSelect)
    {
        $this->db->where('department', $department);
        $this->db->where('status', $statusSelect);
        $query = $this->db->get('tbl_departments');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_departments($data)
    {
        $this->db->insert('tbl_departments', $data);
        return $this->db->insert_id();
    }

    public function get_all_departments()
    {
        $this->db->select("
        d.id AS department_id,
        d.department,
        d.status,
        s.status,
    ");
        $this->db->from("tbl_departments d");
        $this->db->join("tbl_status s", "s.id = d.status", "left");
        $this->db->order_by("d.department", "ASC");
        return $this->db->get()->result();
    }

}

