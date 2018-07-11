<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "mst_menus".
 *
 * @property integer $id
 * @property string $menu_name
 * @property string $menu_type
 * @property string $menu_url
 * @property string $menu_desc
 * @property integer $status
 * @property integer $is_deleted
 * @property integer $menu_parent_id
 */
class MstMenus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mst_menus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_name'], 'required'],
            [['menu_desc'], 'string'],
            [['status', 'is_deleted', 'menu_parent_id'], 'integer'],
            [['menu_name'], 'string', 'max' => 100],
            [['menu_type'], 'string', 'max' => 20],
            [['menu_url'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_name' => 'Menu Name',
            'menu_type' => 'Menu Type',
            'menu_url' => 'Menu Url',
            'menu_desc' => 'Menu Desc',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'menu_parent_id' => 'Menu Parent ID',
        ];
    }

    /*
     *  RBAC New Tables -->
     */
    function getUserMenuListByRole($user_id) {
        $query = "select menu.id as menu_id, menu.menu_name, menu.menu_type, menu.menu_url, menu.menu_parent_id ,menu1.menu_name as parent_name
                    from trn_userrole_settings as setting
                      join trn_userrole_menus as userrole on (userrole.mst_role_id=setting.mst_role_id AND userrole.status=1 AND userrole.is_deleted=0)
                      join mst_menus as menu on (menu.id=userrole.mst_menu_id)
                      left join mst_menus as menu1 on (menu1.id=userrole.menu_parent_id)
                    where setting.mst_user_id=$user_id AND menu.status=1 AND menu.is_deleted=0 order by menu_order;";
        $roles =  Yii::$app->getDb()->createCommand($query)->queryAll();
        return $roles;
    }
    /*
     * function to get role based url
     */
    public function getRoleBasedUrl($user_id) {
        $query = "select default_route from mst_roles where id in(select mst_role_id from trn_userrole_settings where mst_user_id=$user_id);";
        return Yii::$app->getDb()->createCommand($query)->queryAll();
    }
    public function search($params)
    {
        $query = MstMenus::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'menu_name' => $this->menu_name,
            'menu_type' => $this->menu_type,
            'menu_url' => $this->menu_url,
            'menu_desc' => $this->menu_desc,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
            'menu_parent_id' => $this->menu_parent_id,
        ]);

        $query->andFilterWhere(['like', 'menu_name', $this->menu_name]);

        return $dataProvider;
    }
    function actionAssignedMenuList($mst_role_id=0,$parent_id='NULL')
    {
        $query = "select mst_menus.id,mst_menus.menu_name,trn_userrole_menus.menu_order from mst_menus ";
        if(!empty($parent_id) && $parent_id!='NULL')
        $query .= "join trn_userrole_menus on (mst_menus.id=trn_userrole_menus.mst_menu_id and mst_role_id=$mst_role_id and trn_userrole_menus.menu_parent_id=$parent_id)";
        else
        $query .= "join trn_userrole_menus on (mst_menus.id=trn_userrole_menus.mst_menu_id and mst_role_id=$mst_role_id and trn_userrole_menus.menu_parent_id is null)";
        $query .= " where mst_menus.id  in (select mst_menu_id from trn_userrole_menus  where mst_role_id=$mst_role_id )
and mst_menus.menu_type='menu'
and mst_menus.status=1 order by trn_userrole_menus.menu_order ";// echo '<pre>';print_r($query);
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $assigned_list = array();
        foreach($res as $key=>$value)
        {
            $assigned_list[$value['id']] = $value['menu_name'];
        }
         return $assigned_list;
    }
    function actionUnAssignedMenuList($mst_role_id=0,$parent_id='NULL')
    {
        $query = "select mst_menus.id,mst_menus.menu_name,trn_userrole_menus.menu_order from mst_menus ";
        if(!empty($parent_id) && $parent_id!='NULL')
        $query .= "LEFT join trn_userrole_menus on (mst_menus.id=trn_userrole_menus.mst_menu_id and mst_role_id=$mst_role_id and trn_userrole_menus.menu_parent_id =$parent_id)";
        else
        $query .= "LEFT join trn_userrole_menus on (mst_menus.id=trn_userrole_menus.mst_menu_id and mst_role_id=$mst_role_id and trn_userrole_menus.menu_parent_id is  NULL)";
        if(!empty($parent_id) && $parent_id!='NULL')
            $query .= " where mst_menus.id NOT in (select mst_menu_id from trn_userrole_menus  where mst_role_id=$mst_role_id AND menu_parent_id =$parent_id ) ";
        else
            $query .= " where mst_menus.id NOT in (select mst_menu_id from trn_userrole_menus  where mst_role_id=$mst_role_id AND menu_parent_id is null) ";
        $query .= "and mst_menus.menu_type='menu'
and mst_menus.status=1 ORDER BY mst_menus.menu_name "; // echo '<pre>';print_r($query);
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $assigned_list = array();
        foreach($res as $key=>$value)
        {
            $assigned_list[$value['id']] = $value['menu_name'];
        }
        return $assigned_list;
    }
    function actionUserAssignedMenuList($mst_user_id=0,$parent_id='NULL')
    {
        $query = "select mst_menus.id,mst_menus.menu_name,trn_user_menus.menu_order from mst_menus ";
        if(!empty($parent_id) && $parent_id!='NULL')
            $query .= "join trn_user_menus on (mst_menus.id=trn_user_menus.mst_menu_id and mst_user_id=$mst_user_id and trn_user_menus.menu_parent_id=$parent_id)";
        else
            $query .= "join trn_user_menus on (mst_menus.id=trn_user_menus.mst_menu_id and mst_user_id=$mst_user_id and trn_user_menus.menu_parent_id is null)";
        $query .= " where mst_menus.id  in (select mst_menu_id from trn_user_menus  where mst_user_id=$mst_user_id )
and mst_menus.menu_type='menu'
and mst_menus.status=1 order by trn_user_menus.menu_order "; //echo '<pre>';print_r($query);
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $assigned_list = array();
        foreach($res as $key=>$value)
        {
            $assigned_list[$value['id']] = $value['menu_name'];
        }
        return $assigned_list;
    }
    function actionUserUnAssignedMenuList($mst_user_id=0,$parent_id='NULL')
    {
        $query = "select mst_menus.id,mst_menus.menu_name,trn_user_menus.menu_order from mst_menus ";
        if(!empty($parent_id) && $parent_id!='NULL')
            $query .= "LEFT join trn_user_menus on (mst_menus.id=trn_user_menus.mst_menu_id and mst_user_id=$mst_user_id and trn_user_menus.menu_parent_id =$parent_id)";
        else
            $query .= "LEFT join trn_user_menus on (mst_menus.id=trn_user_menus.mst_menu_id and mst_user_id=$mst_user_id and trn_user_menus.menu_parent_id is  NULL)";
        if(!empty($parent_id) && $parent_id!='NULL')
            $query .= " where mst_menus.id NOT in (select mst_menu_id from trn_user_menus  where mst_user_id=$mst_user_id AND menu_parent_id =$parent_id ) ";
        else
            $query .= " where mst_menus.id NOT in (select mst_menu_id from trn_user_menus  where mst_user_id=$mst_user_id AND menu_parent_id is null) ";
        $query .= "and mst_menus.menu_type='menu'
and mst_menus.status=1 ORDER BY mst_menus.menu_name "; // echo '<pre>';print_r($query);
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $assigned_list = array();
        foreach($res as $key=>$value)
        {
            $assigned_list[$value['id']] = $value['menu_name'];
        }
        return $assigned_list;
    }
    function actionGetUserRoles()
    {
        $query = "select id,role_name from mst_roles ";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $role_list = array();
        foreach($res as $key=>$value)
        {
            $role_list[$value['id']] = $value['role_name'];
        }
        return $role_list;
    }
    function actionGetUsernames()
    {
        $query = "select id,username from mst_users where status=1 order by username ";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $role_list = array();
        foreach($res as $key=>$value)
        {
            $role_list[$value['id']] = $value['username'];
        }
        return $role_list;
    }
    function actionGetSubmenus()
    {
        $query = "select id,menu_name from mst_menus where menu_type  ='sdmenu' and status=1 order by menu_name";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $side_list = array();
        foreach($res as $key=>$value)
        {
            $side_list[$value['id']] = $value['menu_name'];
        }
        return $side_list;
    }
    public function getUsersList($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $query = "select employees.employeeid||' - '||users.name as text,users.id as id from users
left join employees on (employees.personnel_id=users.personnel_id)
where users.status='Active' and users.name ilike '%".$q."%'order by users.name "; //echo '<pre>';print_r($query);
        $con= Yii::$app->getDb();
        $res = $con->createCommand($query)->queryAll();
        $out['results'] = array_values($res);
        return $out;
    }
    public function getUserExistingRoles($mst_user_id) {
        $query = "select mst_roles.id,role_name,case when trn_userrole_settings.id is not null then 1 else 0  end role_flag from mst_roles
left join trn_userrole_settings on (mst_roles.id=trn_userrole_settings.mst_role_id and mst_user_id=$mst_user_id)";
        return $con = Yii::$app->getDb()->createCommand($query)->queryAll();
    }
    public function getDaysofMonth() {
        $query = "select * from (
select to_char(days,'dd') as day,to_char(days,'mm') as month,to_char(days,'yyyy') as year from (
select CURRENT_DATE + i as days
from generate_series(date '2016-01-01'- CURRENT_DATE,
     date '2016-12-31' - CURRENT_DATE ) i) as a
) as a where month='11' and year='2016' limit 28";
        return $con = Yii::$app->getDb()->createCommand($query)->queryAll();
    }
    public function getMonthlist() {
        $query = "select to_char(m,'mm') month_id, to_char(m, 'Mon') month_name
from generate_series(
    '2014-01-01'::date, '2014-12-31', '1 month'
) s(m)";
        return $con = Yii::$app->getDb()->createCommand($query)->queryAll();
    }
    public function getAdmlist() {
        $query = "select id,name from mst_patient_coordinators  where status=1 and coordinator_type='ADM'";
        return $con = Yii::$app->getDb()->createCommand($query)->queryAll();
    }
    public function getParticularlist() {
        $query = "select id,particular_name from mst_renewal_payments where status=1";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $particular_list = array();
        foreach($res as $key=>$value)
        {
            $particular_list[$value['id']] = $value['particular_name'];
        }
        return $particular_list;
    }
    public function getYearslist() {
        $query = "select generate_series as id,generate_series as name from (
select generate_series(2016, 2026) ) as a";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $particular_list = array();
        foreach($res as $key=>$value)
        {
            $particular_list[$value['id']] = $value['name'];
        }
        return $particular_list;
    }
    public function getPayRenewList($id='')
    {
        $query = "select mst_renewal_payments.id,particular_name,array_to_string(array_agg(mst_patient_coordinators.name),' / ') as person_incharge  from mst_renewal_payments
join mst_patient_coordinators on (mst_patient_coordinators.id=any(mst_patient_coordinator_id))
where mst_renewal_payments.status=1 ";
        if($id!='')
        {
          $query .="and mst_renewal_payments.id=$id ";
        }
    $query .= "group by particular_name,mst_renewal_payments.id
order by particular_name";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        return $res;
    }
    public function getExistingDue($id=null)
    {
        $query = "select id,case when length(due_day::text)=1 then '0'||due_day::TEXT else due_day::text end||'-'||upper(to_char(to_timestamp (due_month::text, 'MM'), 'TMmon'))||'-'||due_year due_date,
to_char(actual_date,'dd-MON-yyyy') as actual_date,due_status,attachment_path
from trn_renewal_payments
where mst_renewal_payment_id =$id
and status=1
order by due_year,due_month,due_day";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        return $res;
    }
    public function getRenewPaymentdetailsList()
    {
        $query = "select mst_renewal_payment_id,particular_name,
            due_year,
            due_month,
            due_day,
             array_to_string(array_agg(mgr_name order by mgr_name),' / ') as mgr_name
            from (
                select mst_renewal_payment_id,particular_name,
            due_year,
            due_month,
            array_to_string(array_agg(due_day order by due_day),',') as due_day,
             mgr_name from (
                        select mst_renewal_payment_id,mst_renewal_payments.particular_name,
            due_year,
            due_month,
            due_day,
            mst_patient_coordinators.name as mgr_name
             from trn_renewal_payments
            join mst_renewal_payments on (mst_renewal_payments.id=mst_renewal_payment_id)
            join mst_patient_coordinators on (mst_patient_coordinators.id=any(mst_renewal_payments.mst_patient_coordinator_id))
            group by mst_renewal_payment_id,
            due_year,
            due_month,mst_renewal_payments.particular_name,due_day,
            mst_patient_coordinators.name
            order by  mst_renewal_payment_id,due_year,due_month,due_day,mst_renewal_payments.particular_name) as a
            group by mst_renewal_payment_id,particular_name,
            due_year,
            due_month,mgr_name) as a
            group by  mst_renewal_payment_id,particular_name,
            due_year,
            due_month,
            due_day";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        return $res;
    }
    public function getFinYearMonthList($def_fr_year,$def_to_year,$def_fr_month,$def_to_month)
    {
        $query = "select year,case when substring(month_id,1,1)='0' then replace(month_id,'0','') else month_id end month_id,month_name  from (
select to_char(m,'mm') month_id, to_char(m, 'Mon') month_name,to_char(m,'YYYY') as year
from generate_series(
    '$def_fr_year-$def_fr_month-01'::date, '$def_to_year-$def_to_month-28', '1 month'
) s(m)
) as a";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        return $res;
    }
    public function getListOfMonths()
    {
        $query = "select case when substring(month_id,1,1)='0' then replace(month_id,'0','') else month_id end month_id,month_name  from (
select to_char(m,'mm') month_id, to_char(m, 'Month') month_name,to_char(m,'YYYY') as year
from generate_series(
    '2016-01-01'::date, '2016-12-31', '1 month'
) s(m)
) as a
";
        $res = Yii::$app->getDb()->createCommand($query)->queryAll();
        $month_list = array();
        foreach($res as $key=>$value)
        {
            $month_list[$value['month_id']] = $value['month_name'];
        }
        return $month_list;
    }

    public function getUserRoleIdAsArray($user_id) {
        $query = "select mst_role_id from trn_userrole_settings where mst_user_id=$user_id and status=1 and is_deleted=0";
        return Yii::$app->getDb()->createCommand($query)->query();
    }

}
