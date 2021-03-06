<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 轮播图 类型：单页栏目
 */
class BannerController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'lmodelName' => 'Banner', //信息列表模型名称
        'listPldelActionArray' => array(
            'index' => 'del',
        ), //指定批量删除控制器列表控制器名称=>删除控制器名称
    );

    /**
     * 初始化
     */
    function __construct() {
        parent::__construct();
        $controllerConfig = $this->controllerConfig;
        $ModelObj = D($controllerConfig['lmodelName']);
        $this->ModelObj = $ModelObj;
    }

    /**
     * 列表页
     */
    public function index() {
        $controllerConfig = $this->controllerConfig;
        $this->listPldelAction = $controllerConfig['listPldelActionArray'][ACTION_NAME]; //指定批量删除控制器
        $ModelObj = $this->ModelObj;
        //ajax修改数据字段
        if (IS_AJAX) {
            $t = I('get.t');
            $v = I('get.v');
            $id = I('get.i');
            echo $ModelObj->where($ModelObj->getPk() . '=' . $id)->setField($t, $v);
            exit();
        }
        //搜索条件
        $conditions = I('get.');
        $this->conditions = $conditions;
        $results = $ModelObj->search($conditions);
        $lists = $results['list'];
        $this->list = $lists;
        $this->page = $results['page'];
        $this->display();
    }

    /**
     * 增加记录
     */
    public function add() {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data)) {
                $id = $ModelObj->add();
                if ($id > 0) {
                    $this->success('添加成功', S('pefererPage') ? S('pefererPage') : U('index'));
                } else {
                    $this->error($ModelObj->getLastSql());
                }
            } else {
                $this->error($ModelObj->getError());
            }
        } else {
            S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
            $this->display();
        }
    }

    /**
     * 编辑记录
     * @param $id 主键值
     */
    public function edit($id) {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data)) {
                $flag = $ModelObj->save();
                if ($flag !== false) {
                    $this->success('修改成功', S('pefererPage') ? S('pefererPage') : U('index'));
                } else {
                    $this->error($ModelObj->getError());
                }
            } else {
                $this->error($ModelObj->getError());
            }
        } else {
            S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
            $model = $ModelObj->find($id);
            $this->model = $model;
            $this->display();
        }
    }

    /**
     * 删除记录
     */
    public function del() {
        $ModelObj = $this->ModelObj;
        //ajax删除所选数据
        if (IS_AJAX && I('ajaxedit')) {
            $sel = I('post.selidArr');
            if (is_array($sel)) {
                $flag = 0;
                foreach ($sel as $id) {
                    $flag += $ModelObj->where(array($ModelObj->getPk() => $id))->delete();
                }
                $this->success('删除' . $flag . '条数据成功！');
            }
            exit();
        }
        $id = I('get.id');
        S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
        $flag = $ModelObj->delete($id);
        if ($flag > 0) {
            $this->success('删除成功', S('pefererPage') ? S('pefererPage') : U('index'));
        } else {
            $this->error($ModelObj->getError());
        }
    }

}