<?php
/**
 * author: wxf
 */

namespace app\modules\admin\models;

use Yii;
use app\models\Admin;
class AdminMenu
{
    //子账号显示的菜单路由
    public $route = [
        'admin/user/me',
        'admin/app/index',
        'admin/app/recycle',
        'admin/cache/index',
        'admin/app/renew',
       'admin/app/recode',
         'admin/app/refer',
       'admin/app/people',
    ];
 //子账号审核员显示的菜单路由
    public $routes = [
        'admin/user/me',
        'admin/app/index',
        'admin/app/recycle',
        'admin/cache/index',
        'admin/app/renew',
        'admin/app/recode',
        'admin/app/people',
        'admin/app/refer',
        'admin/app/sure',
    ];
    public function getMenu()
    {
        $data = Yii::$app->getCache()->get($this->getMenuCacheKey());
        if ($data) {
            return $data;
        }

        $menu = Menu::getMenu();
      
       	$data = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
        if($data->refer == 1){
            $menuList = $this->resetList($menu, $this->routes);
        }else{
            $menuList = $this->resetList($menu, $this->route);
        }
      
        $menuList = $this->delete($menuList);


        Yii::$app->getCache()->set($this->getMenuCacheKey(), $menuList, 1800);
        return $menuList;
    }

    public function resetList($list, $route)
    {
        foreach ($list as $k => $item) {
            if (Yii::$app->admin->id == 1) {
                $list[$k]['show'] = true;
            } else {
                if (in_array($item['route'], $route)) {
                    $list[$k]['show'] = true;
                } else {
                    $list[$k]['show'] = false;
                }
            }


            if (isset($item['children']) && is_array($item['children'])) {
                $list[$k]['children'] = $this->resetList($item['children'], $route);
                foreach ($list[$k]['children'] as $i) {
                    if ($i['show']) {
                        $list[$k]['route'] = $i['route'];
                        $list[$k]['show'] = true;
                        break;
                    }
                }
            }
        }

        return $list;
    }


    public function delete($menuList)
    {
        foreach ($menuList as $k1 => $item) {
            if (isset($item['children'])) {
                $menuList[$k1]['children'] = $this->delete($item['children']);
            }

            if ($item['show'] == false) {
                unset($menuList[$k1]);
            }
        }

        return $menuList;
    }

    /**
     * 现只用于左侧菜单缓存
     * @return string
     */
    public function getMenuCacheKey()
    {
        //用户accessToken 作为用户菜单的唯一标识符
        $adminId = Yii::$app->admin->id;
        $accessToken = Yii::$app->admin->identity->access_token;
        $cacheKey = 'ind-' . $adminId . $accessToken;

        return $cacheKey;
    }
}
