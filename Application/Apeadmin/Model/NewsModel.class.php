<?php
namespace Apeadmin\Model;

use Think\Model\AdvModel;

class NewsModel extends AdvModel {
    /**
     * 搜索数组
     * @var array
     */
    public $searchFields = array(
        'title' => 'like',
        'is_show' => 'eq',
        'category_id' => 'eq',
        'create_time' => 'between',
        'status' => 'eq',
    );

    /**
     * 字段验证
     * @var array
     */
    protected $_validate = array(
        array('title', 'require', '标题必须填写！', 1),
        array('is_show', array(0, 1), '请设置是否显示！', 1, 'in')
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('create_time', 'strtotime', 3, 'function'), // 在新增和编辑的时候回调方法
    );

    /**
     * 序列化字段
     * @var array
     */
    protected $serializeField = array(
        'seo_meta' => array('seo_title', 'seo_keywords', 'seo_description'),
    );

    /**
     * 新增或修改时可调用指定的序列化字段
     * @param $data
     */
    public function serializeSet($data) {
        $serializeField = $this->serializeField;
        foreach ($serializeField as $k => $v) {
            foreach ($v as $i => $m) {
                $this->$m = $data[$m];
            }
        }
    }

    /**
     * 搜索符合条件的记录
     * @param $conditions 条件数组
     * @param string $order 排序
     * @param int $number 每页条数
     * @param bool $all 是否查找所有
     * @return mixed
     */
    public function search($conditions, $order = 'create_time DESC', $all = false, $number = 0) {
        $dataFields = M($this->name)->getDbFields();
        $condition = D('Dbsearch', 'Service')->get_condition($conditions, $dataFields, $this->searchFields);
        if (!$all) {
            if ($condition == 1) {
                $condition = array('status' => 1);
            } else {
                $condition['status'] = 1;
            }
            $count = $this->where($condition)->count();
            $number = $number ? $number : C('PAGE_NUM');
            Vendor('Page.Page');
            $Page = new \Page($count, $number);
            $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();
            $list = $this->where($condition)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $array['list'] = $list;
            $array['page'] = $show;
            return $array;
        } else {
            return $this->where($condition)->order($order)->select();
        }
    }
}