<?php
/**
 * Master アクション
 *
 * @author hideki.takahashi
 */
class Controller_Master extends Controller_JsonCommon {
    
    
/*    
    "item: [
　　id, id, id, ...
],
weapon: [
　　id, id, id, ...
],
avatar: [
　　id, id, id, ...
],
skill: [
　　id, id, id, ...
],
monster: [
　　id, id, id, ...
],
job[
　　id,id,id,...
]
"
  */  
    
    /*
     *  全部のマスタ取得
     */
    public function get_index(){
        
        //アイテムマスタ
        $model_item_mst = new Model_ItemMst();
        $list = $model_item_mst->getConsumerItem();
 		$this->data['item'] = $list;

        //武器マスタ
        $list = $model_item_mst->getWeapon();
 		$this->data['weapon'] = $list;
        
        //アバターマスタ
        $list = $model_item_mst->getAvatar();
 		$this->data['avatar'] = $list;
        
        //ショップマスタ
		$model_shop_list = new Model_ShopList();
		$list = $model_shop_list->getShopList();
 		$this->data['shop'] = $list;
        
        //モンスターマスタ
        $model_monser_mst = new Model_MonsterMst();
        $list = $model_monser_mst->getMonster();
        $this->data['monster'] = $list;

        // スキルマスタ
        $model_skill_mst = new Model_SkillMst();
		$list = $model_skill_mst->getSkill();
 		$this->data['skill'] = $list;

        // ジョブマスタ
		$model_job_mst = new Model_jobMst();
		$list = $model_job_mst->getJob();
 		$this->data['job'] = $list;
        
        
        
        //出力
        $this->response($this->data);
        
    }

    /*
     *  アイテムマスタを取得（item_mst [ C ] 区分）
     */
    public function get_item(){
        
        $model_item_mst = new Model_ItemMst();
        $list = $model_item_mst->getConsumerItem();
 		$this->data['item'] = $list;
    }
    
    /*
     *  武器マスタを取得（item_mst [ W ] 区分）
     */
    public function get_weapon(){
        
        $model_item_mst = new Model_ItemMst();
        $list = $model_item_mst->getWeapon();
 		$this->data['weapon'] = $list;
    }

    
    /*
     *  アバターマスタを取得（item_mst [ A ] 区分）
     */
    public function get_avatar(){
        $model_item_mst = new Model_ItemMst();
        $list = $model_item_mst->getAvatar();
 		$this->data['avatar'] = $list;
    }
    
    
	/**
	 * ショップリストを返す。
	 */
	public function get_shop() {
		$model_shop_mst = new Model_ShopList();
		$list = $model_shop_mst->getShopList();
 		$this->data['shop'] = $list;
	}
    
	/**
	 * モンスターマスタを返す。
	 */
	public function get_monster() {
		$model_monster_mst = new Model_MonsterMst();
		$list = $model_monster_mst->getMonster();
 		$this->data['monster'] = $list;
	}

    /**
	 * スキルマスタを返す。
	 */
	public function get_skill() {
		$model_skill_mst = new Model_SkillMst();
		$list = $model_skill_mst->getSkill();
 		$this->data['skill'] = $list;
	}
    
    /**
	 * ジョブマスタを返す。
	 */
	public function get_job() {
		$model_job_mst = new Model_jobMst();
		$list = $model_job_mst->getJob();
 		$this->data['job'] = $list;
	}
    
}
