<?php
/**
 * JSONレスポンスを行う共通コントローラ。
 * 各アクションの結果に、共通レスポンスをつけて JSON で出力する。
 *
 * @author hideki.takahashi
 */
use Log\Log;

class Controller_JsonCommon extends Controller_Rest {
	/** 共通パラメータ用プロパティ */
	private		$res = array();
	/** アクションの個別出力用プロパティ */
	protected	$data = array();

	/** 例外処理用のプロパティ */
	public static $exception = null;

	/**
	 * エラー補足用アクション。
	 * routes.php でルーティングをこのアクションに指定してください。
	 * @see fuel/app/config/development/routes.php
	 */
	public final function action_error() {
	}

	/**
	 * JSONの共通レスポンスを生成する。
	 * この関数は通常処理時、エラー発生時ともにコールされる。
	 * @param string $response 任意。レスポンス文字列
	 * @return void
	 */
	private final function makeCommonResponse($response = null) {
		$res = array();
		$res['reflect'] = '1';

		$res['status']	= 'OK';
		if (!is_null(self::$exception) ) {
			$exp = self::$exception;

			$res['status'] = 'NG';
			$res['message'] = "システムエラーが発生しました ({$exp->getCode()})";

			Log::error($exp);
		}

		// game_config テーブルデータを見てメンテ判定を行う
		$modelGameconfig = new Model_Gameconfig();
		if ($modelGameconfig->isMaintenance()) {
			$res['stop']['message'] = 'メンテ中です';
			$res['stop']['restart'] = 0;
		}
		// ポップアップ
		if (Config::get('pop', false)) {
			$res['pop'] = Config::get('pop');
		}

		// TODO 必要？
		if (!is_null($response)) {
			$res['body'] = $response;
		}

		$this->res = $res;
	}

	/**
	 * Controller_JsonCommon を継承したクラスの最後に呼ばれる処理。
	 *
	 * @see Fuel\Core.Controller_Rest::after()
	 */
	public final function after($response) {
		try {
			// エラー時の after でエラーを出さないように
			$this->makeCommonResponse($response);
		} catch (Exception $e) {
			Log::error($e);
		}

		$ret = array_merge($this->res, $this->data);
		return $this->response($ret);
	}

}
