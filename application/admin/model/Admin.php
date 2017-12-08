<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:48
 */
namespace app\admin\model;

use think\Model;
use think\Validate;
use think\Db;
use think\Loader;
use think\Session;
use app\common\controller\Data;

class Admin extends Model {
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime  = false;
    protected $updateTime = 'login_time';
    public $request;

    /**
     *设置更新时间字段值为不自动更新
     */
    public function noUpdate() {
        $this->updateTime = false;
    }

    /******************************************************管理员*******************************************************************/

    /**
     * 获取管理员列表
     * @return array
     */
    public function getUsers() {
        $limit = $this->request->get('limit');
        $page = $this->request->get('page');
        $join = [
            ['ceb_auth_group_access b','a.id=b.uid'],
//                ['ceb_auth_group c','b.group_id=c.id']
        ];
//        $field = "*,a.status as status,c.status as group_status,a.id as id";
        $field = "";
        $admin = new \app\admin\model\Admin();
        $data = $admin->alias('a')->page($page,$limit)->join($join)->field($field)->select();
        $count = $admin->alias('a')->join($join)->count();
        if ($count>0) {
            foreach ($data as $k => $record) {
                if ($record['group_id']==="") {
                    $data[$k]['title'] = "未分组";
                }else{
                    $auth_group = Db::name('auth_group');
                    $res = $auth_group->where('id','in',$record['group_id'])->select();
                    $temp = null;
                    foreach ($res as $val) {
                        $temp[]=$val['title'];
                    }
                    $data[$k]['title'] = implode(',',$temp);
                }
            }
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $data,
            ];
            return $data_format;
        }else{
            return feedback('0001','无数据！');
        }
    }

    /**
     * 删除用户
     * @return array
     */
    public function delUsers() {
        $id = $this->request->post('id');
        $data = $this->request->post('data');
        $admin = Loader::model('admin');
        $auth_group_access = Db::name('auth_group_access');
        $res = false;
        if ($data===null && $admin::get($id)['status']!=9) {
            $res = $admin::destroy(['id'=>$id]) or 0;
            $res ? $auth_group_access->where('uid='.$id)->delete() : "" ;
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                if ($admin::get($val)['status']==9) break;
                $res = $admin::destroy(['id'=>$val]) or 0;
                $res ? $auth_group_access->where('uid='.$val)->delete() : "" ;
            }
        }
        if ($res) {
            return feedback('0000','删除成功！');
        }else{
            return feedback('0001','删除失败！');
        }
    }

    /**
     * 获取用户组
     * @return array
     */
    public function getAuthGroup() {
        $auth_group = Db::name('auth_group');
        $data = $auth_group->select();  //->where('type','<>','-1')
        return [
            'code' => '0000',
            'data' => $data
        ];
    }

    /**
     * 添加用户
     * @return array
     */
    public function addUser() {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        $status = $this->request->post('status');
        $gid = $this->request->post('gid/a') ? implode(',',$this->request->post('gid/a')) : "";
        $validate = new Validate([
            'username' => 'require',
            'password' => 'require'
        ]);
        if (!$validate->batch()->check(['username'=>$username,'password'=>$password])) {
            return [
                'code' => '0003',
                'msg' => $validate->getError()
            ];
        }
        $admin = new \app\admin\model\Admin();
        $auth_group_access = Db::name('auth_group_access');
        Db::startTrans();
        try{
            $admin->data(['username'=>$username,'password'=>md5($password),'status'=>$status]);
            $admin->noUpdate();
            $admin->isUpdate(false)->save();
            $res = $auth_group_access->insert(['uid'=>$admin->id,'group_id'=>$gid]);
            if ($res) {
                Db::commit();
                return [
                    'code' => '0000',
                    'msg' => '用户添加成功！'
                ];
            }
        }catch (\Exception $e) {
            Db::rollback();
            return [
                'code' => '0002',
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 修改用户信息
     * @return array
     */
    public function editUser() {
        $username = $this->request->post('username');
        $uid = $this->request->post('uid');
        $status = $this->request->post('status');
        $gid = $this->request->post('gid/a') ? implode(',',$this->request->post('gid/a')) : "";
        $admin = new \app\admin\model\Admin();
        $auth_group_access = Db::name('auth_group_access');
        Db::startTrans();
        try{
            $res = $admin::get($uid);
            $admin->noUpdate();
            if ($res['status'] == 9) {
                $admin->isUpdate(true)->save(['username'=>$username],['id'=>$uid]);
                Session::set('username',$username);
            }else{
                $admin->isUpdate(true)->save(['status'=>$status,'username'=>$username],['id'=>$uid]);
            }
            $auth_group_access->where(['uid'=>$uid])->update(['group_id'=>$gid]);
            Db::commit();
            return [
                'code' => '0000',
                'msg' => '用户修改成功！'
            ];
        }catch (\Exception $e) {
            Db::rollback();
            return [
                'code' => '0002',
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 搜索用户
     * @return array
     */
    public function search() {
        $conditions = $this->request->post('data/a');
        $page =  $this->request->post('page');
        $limit =  $this->request->post('limit');
        $where = [];
        $join = [
            ['ceb_auth_group_access b','a.id=b.uid']
        ];
        foreach ($conditions as $k => $obj) {
            if (preg_match('/^username$/i',$obj['name'])) {
                if ($obj['value'] == "") { continue; }
                $where['username'] = ['like','%'.$obj['value'].'%'];
            }elseif (preg_match('/time_begin$/i',$obj['name'])) {
                if ($obj['value']==''&&$conditions[$k+1]['value']=='') {
                    continue;
                }elseif ($obj['value']==''&&$conditions[$k+1]['value']!='') {
                    $obj['value'] = $conditions[$k+1]['value'];
                }elseif($obj['value']!=''&&$conditions[$k+1]['value']=='') {
                    $conditions[$k+1]['value'] = $obj['value'];
                }
                $fieldname = substr($obj['name'],0,-6);
                if ($obj['value'] == $conditions[$k+1]['value']) {
                    $where['DATE_FORMAT('.$fieldname.',\'%Y-%m-%d\')'] = ['exp','=\''.$obj['value'].'\''];
                }else{
//                    $where[$this->type[$type_index]['whereKey'].$fieldname] = ['between time',[$obj['value'],$conditions[$k+1]['value']]];
                    $where['DATE_FORMAT('.$fieldname.',\'%Y-%m-%d\')'] = ['exp','between \''.$obj['value'].'\' and '.'\''.$conditions[$k+1]['value'].'\''];
                }
            }
        }
        $field = "";
        $admin = new \app\admin\model\Admin();
        $data = $admin->alias('a')->join($join)->where($where)->page($page,$limit)->field($field)->select();
        $count = $admin->alias('a')->join($join)->where($where)->count();
        if ($count>0) {
            foreach ($data as $k => $record) {
                if ($record['group_id']==="") {
                    $data[$k]['title'] = "未分组";
                }else{
                    $auth_group = Db::name('auth_group');
                    $res = $auth_group->where('id','in',$record['group_id'])->select();
                    $temp = null;
                    foreach ($res as $val) {
                        $temp[]=$val['title'];
                    }
                    $data[$k]['title'] = implode(',',$temp);
                }
            }
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $data,
            ];
            return $data_format;
        }else{
            return [
                'code' => '0001',
                'msg' => '无数据！'
            ];
        }
    }

    /******************************************************用户组*******************************************************************/

    /**
     * 获取用户组列表
     * @return array
     */
    public function getGroups() {
        $limit = $this->request->get('limit');
        $page = $this->request->get('page');
        $join = [
            ['admin b','a.uid=b.id']
        ];
        $field = "username";
        $auth_group = Db::name('auth_group');
        $data = $auth_group->page($page,$limit)->select(); //->alias('a')->join($join)
        $count = $auth_group->count();  //->alias('a')->join($join)
        if ($count>0) {
            $i = 1;
            if ($page>1) {
                $i = ($page-1)*$limit+1;
            }
            $auth_group_access = Db::name('auth_group_access');
            $admin = new \app\admin\model\Admin();
            foreach ($data as $k => $record) {
                $data[$k]['autonum'] = $i++;
                $res = $auth_group_access->alias('a')->join($join)->where('group_id','exp',"REGEXP '^{$record['id']}$|^{$record['id']},|,{$record['id']},|,{$record['id']}$'")->field($field)->select();
                if (count($res)>0) {
                    $username = [];
                    foreach ($res as $row) {
                        $username[] = $row['username'];
                    }
                    $username = implode(',',$username);
                    $data[$k]['username'] = $username;
                }else{
                    $data[$k]['username'] = "未添加用户";
                }
            }
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $data,
            ];
            return $data_format;
        }else{
            return [
                'code' => '0001',
                'msg' => '无数据！'
            ];
        }
    }

    /**
     * 添加用户组
     * @return array
     */
    public function addGroup() {
        $title = $this->request->post('title');
        $module = $this->request->post('module');
        $description = $this->request->post('description');
        $status = $this->request->post('status');
        $type = $this->request->post('type',1);
        $validate = new Validate([
            'title' => 'require',
            'module' => 'require',
            'status' => 'require|length:1',
        ]);
        if (!$validate->batch()->check(['title'=>$title,'module'=>$module,'status'=>$status])) {
            return [
                'code' => '0003',
                'msg' => $validate->getError()
            ];
        }
        $auth_group = Db::name('auth_group');
        Db::startTrans();
        try{
            $res = $auth_group->insert(['title'=>$title,'description'=>$description,'module'=>$module,'status'=>$status,'type'=>$type]);
            if ($res) {
                Db::commit();
                return feedback('0000','用户添加成功！');
            }
        }catch (\Exception $e) {
            Db::rollback();
            return feedback('0002',$e->getMessage());
        }
    }

    /**
     * 编辑用户组信息
     * @return array
     */
    public function editGroup() {
        $title = $this->request->post('title');
        $module = $this->request->post('module');
        $description = $this->request->post('description');
        $status = $this->request->post('status');
        $gid = $this->request->post('gid');
        $auth_group = Db::name('auth_group');
        Db::startTrans();
        try{
            $auth_group->where(['id'=>$gid])->update(['title'=>$title,'module'=>$module,'description'=>$description,'status'=>$status]);
            Db::commit();
            return feedback('0000','用户组更改成功！');

        }catch (\Exception $e) {
            Db::rollback();
            return feedback('0002',$e->getMessage());
        }
    }

    /**
     * 删除用户组
     * @return array
     */
    public function delGroups() {
        $id = $this->request->post('id');
        $data = $this->request->post('data');
        $auth_group = Db::name('auth_group');
        $auth_group_access = Db::name('auth_group_access');
        $res = false;
        if ($data===null) {
            $res = $auth_group->delete(['id'=>$id]) or 0;
            if ($res) {
                $this->removeGroup($auth_group_access, $id);
            }
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                $res = $auth_group->delete(['id'=>$val]) or 0;
                if ($res) {
                    $this->removeGroup($auth_group_access, $val);
                }
            }
        }
        if ($res) {
            return feedback('0000','删除成功！');
        }else{
            return feedback('0001','删除失败！');
        }
    }

    /**
     * 移除auth_group_access表中的组号
     * @param $auth_group_access
     * @param $id
     */
    public function removeGroup($auth_group_access, $id,$uid=null) {
        if ($uid!==null) {
            $datas = $auth_group_access->where('uid',$uid)->select();
        }else{
            $datas = $auth_group_access->where('group_id','exp',"REGEXP '^{$id}$|^{$id},|,{$id},|,{$id}$'")->select();
        }
        if (count($datas)>0) {
            foreach ($datas as $row) {
                $ndata = array_diff(explode(',',$row['group_id']),[$id]);
                $auth_group_access->where(['uid'=>$row['uid']])->update(['group_id'=>implode(',',$ndata)]);
            }
            return true;
        }
    }

    /**
     * 获取用户组成员列表
     * @return array
     */
    public function getGroupMembers() {
        $limit = $this->request->param('limit');
        $page = $this->request->param('page');
        $gid = $this->request->param('gid');
        $type = $this->request->param('type');
        $join = [
            ['ceb_auth_group_access b','a.id=b.uid']
        ];
        $field = "";
        $admin = new Admin();
        $data = $admin->alias('a')->join($join)->field($field)->select();
        $count = $admin->alias('a')->join($join)->count();
        if ($count>0) {
            $member = [];
            $nomember = [];
            foreach ($data as $k => $record) {
                if ($record['group_id']==="") {
                    $data[$k]['title'] = "未分组";
                }else{
                    $auth_group = Db::name('auth_group');
                    $res = $auth_group->where('id','in',$record['group_id'])->select();
                    $temp = null;
                    foreach ($res as $val) {
                        $temp[]=$val['title'];
                    }
                    $data[$k]['title'] = implode(',',$temp);
                }
            }
            foreach ($data as $row) {
                if (in_array($gid,explode(',',$row['group_id']))) {
                    $member[] = $row;
                }else{
                    $nomember[] = $row;
                }
            }
            $type === 'mem' ? $data = $member : $data = $nomember;
            $type === 'mem' ? $count = count($member) : $count = count($nomember);
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $data,
            ];
            return $data_format;
        }else{
            return feedback('0001','无数据！');
        }
    }

    /**
     * 移除用户组成员
     * @return array
     */
    public function removeGroupMember() {
        $gid = $this->request->post('gid');
        $uid = $this->request->post('uid');
        $data = $this->request->post('data');
        $auth_group_access = Db::name('auth_group_access');
        if ($data===null) {
            $res = $this->removeGroup($auth_group_access, $gid,$uid);
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                $res = $this->removeGroup($auth_group_access, $gid,$val);
            }
        }

        if ($res === true) {
            return feedback('0000','移除成功！');
        }else{
            return feedback('0001','移除出错！');
        }
    }

    /**
     * 添加会员到组
     * @return array
     */
    public function addMember() {
        $gid = $this->request->post('gid');
        $uid = $this->request->post('uid');
        $data = $this->request->post('data');
        $auth_group_access = Db::name('auth_group_access');
        $res = false;
        if ($data===null) {
            $datas = $auth_group_access->where('uid',$uid)->find();
            if (count($datas['group_id']>0)) {
                $datas['group_id'].= ','.$gid;
            }else{
                $datas['group_id'] = $gid;
            }
            $res = $auth_group_access->where(['uid'=>$datas['uid']])->update(['group_id'=>$datas['group_id']]);
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                $datas = $auth_group_access->where('uid',$val)->find();
                if (count($datas['group_id']>0)) {
                    $datas['group_id'].= ','.$gid;
                }else{
                    $datas['group_id'] = $gid;
                }
                $res = $auth_group_access->where(['uid'=>$datas['uid']])->update(['group_id'=>$datas['group_id']]);
            }
        }
        if ($res) {
            return feedback('0000','移至组成功！');
        }else{
            return feedback('0001','移至组出错！');
        }
    }

    /**
     * 获取授权列表
     * @return array
     */
    public function getRules() {
        $gid = $this->request->post('gid');
        $type='level';
        $order='';
        $name='title';
        $child='id';
        $parent='pid';
        $auth_rule = Db::name('auth_rule');
        if ($gid === null) {
            // 判断是否需要排序
            if(empty($order)){
                $data=$auth_rule->select();
            }else{
                $data = $auth_rule->order($order.' is null,'.$order)->select();
            }
//            $count = $auth_rule->count();
            //获取树形或者结构数据
            if($type==='tree'){
                $data=Data::tree($data,$name,$child,$parent);
            }elseif($type==="level"){
                $data=Data::channelLevel($data,0,'&nbsp;',$child);
            }
        }else{
            $auth_group = Db::name('auth_group');
            $data = $auth_group->where(['id'=>$gid])->field('rules')->find();
        }
        if (count($data)>0) {
            return feedback('0000','success',$data);
        }else{
            return feedback('0001','fail');
        }
    }

    /**
     * 修改用户组权限
     * @return array
     */
    public function editRules() {
        $gid = $this->request->post('gid');
        $data = $this->request->post('data/a');
        $auth_group = Db::name('auth_group');
        $rules = [];
        if ($data === null) {
            $rules = '';
        }else{
            foreach ($data as $val) {
                $rules[] = $val['value'];
            }
            $rules = implode(',',$rules);
        }
        $res = $auth_group->where('id',$gid)->update(['rules'=>$rules]);
        if ($res>=0) {
            return feedback('0000','授权更新成功！');
        } else{
            return feedback('0001','未知错误！');
        }
    }

    /****************************************************权限**********************************************************/

    /**
     * 获取权限数据
     * @param  string $type  tree获取树形结构 level获取层级结构
     * @param  string $order 排序方式
     * @return array         结构数据
     */
    public function getPermissionData(){
        $limit = $this->request->get('limit');
        $page = $this->request->get('page');
        $type='tree';
        $order='';
        $name='title';
        $child='id';
        $parent='pid';
        $auth_rule = Db::name('auth_rule');
        $i = 1;
        if ($page>1) {
            $i = ($page-1)*$limit+1;
        }
        // 判断是否需要排序
        if(empty($order)){
            $data=$auth_rule->select();
        }else{
            $data = $auth_rule->order($order.' is null,'.$order)->select();
        }
        $count = $auth_rule->count();
        //获取树形或者结构数据
        if($type==='tree'){
            $data=Data::tree($data,$name,$child,$parent);
        }elseif($type==="level"){
            $data=Data::channelLevel($data,0,'&nbsp;',$child);
        }
        $fdata = array_values($data);

        //分页时启用
//        $fdata = [];
//        $t = ($page-1)*$limit;
//        for ($j = 0;$j<$limit;$j++) {
//            if ($page>1) {
//                if (!isset($data[$t])) break;
//                $fdata[$j] = $data[$t];
//                $t++;
//            }else{
//                if (!isset($data[$j])) break;
//                $fdata[$j] = $data[$j];
//            }
//        }
        if ($count>0) {
            foreach ($fdata as $k => $row) {
                $fdata[$k]['autonum'] = $i++;
            }
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $fdata,
            ];
            return $data_format;
        }else{
            return feedback('0001','无数据！');
        }
    }

    /**
     * 修改权限
     * @param $request
     * @return array
     */
    public function editPermission() {
        $title = $this->request->post('title');
        $name = $this->request->post('name');
        $status = $this->request->post('status');
        $condition = $this->request->post('condition',1);
        $id = $this->request->post('id');
        $validate = new Validate([
            'title' => 'require',
            'name' => 'require|/^\w+\/\w?/',
            'status' => 'require|length:1'
        ]);
        if (!$validate->batch()->check(['title'=>$title,'name'=>$name,'status'=>$status])) {
            return feedback('0003',$validate->getError());
        }
        $auth_rule = Db::name('auth_rule');
        $name = implode('/',array_map(function($val){return ucwords($val);},explode('/',$name)));
        Db::startTrans();
        try{
            $res = $auth_rule->where(['id'=>$id])->update(['title'=>$title,'name'=>$name,'status'=>$status,'condition'=>$condition]);
            if ($res | $res === 0) {
                Db::commit();
                return feedback('0000','权限修改成功！');
            }
        }catch (\Exception $e) {
            Db::rollback();
            return feedback('0002',$e->getMessage());
        }
    }

    /**
     * 添加权限
     * @return array
     */
    public function addPermission() {
        $module = $this->request->post('module');
        $title = $this->request->post('title');
        $name = $this->request->post('name');
        $status = $this->request->post('status');
        $condition = $this->request->post('condition',1);
        $pid = $this->request->post('pid',0);
        $validate = new Validate([
            'title' => 'require',
            'module' => 'require',
            'name' => 'require|/^\w+\/\w?/',
            'status' => 'require|length:1'
        ]);
        if (!$validate->batch()->check(['title'=>$title,'name'=>$name,'module'=>$module,'status'=>$status])) {
            return feedback('0003',$validate->getError());
        }
        $name = ucfirst($module.'/'.implode('/',array_map(function($val){return ucfirst($val);},explode('/',$name))));
        $auth_rule = Db::name('auth_rule');
        Db::startTrans();
        try{
            $res = $auth_rule->insert(['pid'=>$pid,'title'=>$title,'module'=>$module,'name'=>$name,'status'=>$status,'condition'=>$condition]);
            if ($res) {
                Db::commit();
                return feedback('0000','权限添加成功！');
            }
        }catch (\Exception $e) {
            Db::rollback();
            return feedback('0002',$e->getMessage());
        }
    }

    /**
     * 删除权限
     * @return array
     */
    public function delPermission() {
        $id = $this->request->post('id');
        $data = $this->request->post('data');
        $auth_rule = Db::name('auth_rule');
        $res = false;
        if ($data===null) {
            if ($auth_rule->where('pid',$id)->count()>0) {
                return feedback('0002','请先删除子权限！');
            }
            $res = $auth_rule->delete(['id'=>$id]) or 0;
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                if ($auth_rule->where('pid',$val)->count()>0) {
                    return feedback('0003','请先删除子权限！');
                }
                $res = $auth_rule->delete(['id'=>$val]) or 0;
                if (!$res) break;
            }
        }
        if ($res) {
            return feedback('0000','删除成功！');
        }else{
            return feedback('0001','删除失败！');
        }
    }
}