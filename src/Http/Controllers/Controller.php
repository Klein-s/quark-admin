<?php

namespace QuarkCMS\QuarkAdmin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use QuarkCMS\QuarkAdmin\Container;
use QuarkCMS\QuarkAdmin\Card;
use QuarkCMS\QuarkAdmin\Table;
use QuarkCMS\QuarkAdmin\Form;

class Controller extends BaseController
{
    /**
     * 页面标题
     *
     * @var string
     */
    protected $title = '默认页面';

    /**
     * 页面二级标题
     *
     * @var string
     */
    protected $subTitle = false;

    /**
     * 页面面包屑导航
     *
     * @var array
     */
    protected $breadcrumb = false;

    /**
     * 获取标题
     *
     * @param  void
     * @return string
     */
    protected function title()
    {
        return $this->title;
    }

    /**
     * 获取二级标题
     *
     * @param  void
     * @return string
     */
    protected function subTitle()
    {
        return $this->subTitle;
    }

    /**
     * 获取面包屑导航
     *
     * @param  void
     * @return array
     */
    protected function breadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * 列表页面
     *
     * @param Request $request
     *
     * @return json
     */
    public function index(Request $request)
    {
        $table = $this->table();

        // 初始化容器
        $container = new Container();

        // 设置标题
        $container->title($this->title());

        // 设置二级标题
        $container->subTitle($this->subTitle());

        // 设置面包屑导航
        $container->breadcrumb($this->breadcrumb());

        // 设置内容
        $container->content($table);

        return success('获取成功！','',$container);
    }

    /**
     * 详情页面
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->get('id');

        if(empty($id)) {
            return error('参数错误！');
        }

        $show = $this->detail($id);

        $content = Quark::content()
        ->title($this->title())
        ->subTitle($this->subTitle())
        ->description($this->description())
        ->breadcrumb($this->breadcrumb())
        ->body($show->render());

        return success('获取成功！','',$content);

    }

    /**
     * 创建页面
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $form = $this->form();

        // 初始化容器
        $container = new Container();

        // 设置标题
        $container->title($this->title());

        // 设置二级标题
        $container->subTitle($this->subTitle());

        // 设置面包屑导航
        $container->breadcrumb($this->breadcrumb());

        // 设置内容
        $container->content($form);

        return success('获取成功！','',$container);
    }

    /**
     * 创建数据方法
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = $this->form()->store();

        $action = \request()->route()->getName();
        $action = Str::replaceFirst('api/','',$action);
        $action = Str::replaceLast('/store','/index',$action);

        $url = "/quark/engine?api=".$action."&component=table";

        if($result['status'] == 'success') {
            return success('操作成功！',$url);
        } else {
            return error($result['msg']);
        }
    }

    /**
     * 编辑页面
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');

        if(empty($id)) {
            return error('参数错误！');
        }

        $form = $this->form()->edit($id);

        $content = Quark::content()
        ->title($this->title())
        ->subTitle($this->subTitle())
        ->description($this->description())
        ->breadcrumb($this->breadcrumb())
        ->body(['form'=>$form->render()]);

        return success('获取成功！','',$content);
    }

    /**
     * 更新数据方法
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $result = $this->form()->update();

        $action = \request()->route()->getName();
        $action = Str::replaceFirst('api/','',$action);
        $action = Str::replaceLast('/update','/index',$action);

        $url = "/quark/engine?api=".$action."&component=table";

        if($result['status'] == 'success') {
            return success('操作成功！',$url);
        } else {
            return error($result['msg']);
        }
    }

    /**
     * 执行行为方法
     *
     * @param  Request  $request
     * @return Response
     */
    public function action(Request $request)
    {
        $id = request('id');
        $key = request('key');

        if(empty($id) || empty($key)) {
            return error('参数错误！');
        }

        if(is_array($id)) {

            // 批量操作
            $result = $this->table()->executeBatchAction($id,$key);
        } elseif($id === '{id}') {

            // 工具栏操作
            $result = $this->table()->executeToolBarAction($key);
        } else {
            
            // 行操作
            $result = $this->table()->executeRowAction($id,$key);
        }

        if($result) {
            return success('操作成功！');
        } else {
            return error('操作失败！');
        }
    }
}
