<?php
	class qa_html_theme_layer extends qa_html_theme_base
	{
		function head_custom()
		{
			qa_html_theme_base::head_custom();

			$this->output('
<style>
'.qa_opt('marker_plugin_css_2').'
</style>');
		}

		function post_avatar($post, $class, $prefix=null)
		{
			if(isset($post['avatar']) && (($class == 'qa-q-view' && qa_opt('marker_plugin_a_qv')) || ($class == 'qa-q-item' && qa_opt('marker_plugin_a_qi')) || ($class == 'qa-a-item' && qa_opt('marker_plugin_a_a')) || ($class == 'qa-c-item' && qa_opt('marker_plugin_a_c')))) {
				$uid = $post['raw']['userid'];
				$image = $this->get_role_marker($uid,1,$post);
				$post['avatar'] = $image.@$post['avatar'];
			}
			qa_html_theme_base::post_avatar($post, $class, $prefix);
		}

		function post_meta($post, $class, $prefix=null, $separator='<BR/>')
		{
			if(isset($post['who']) && (($class == 'qa-q-view' && qa_opt('marker_plugin_w_qv')) || ($class == 'qa-q-item' && qa_opt('marker_plugin_w_qi')) || ($class == 'qa-a-item' && qa_opt('marker_plugin_w_a')) || ($class == 'qa-c-item' && qa_opt('marker_plugin_w_c')))) {
				$handle = strip_tags($post['who']['data']);
				$uid = $this->getuserfromhandle($handle);
				$image = $this->get_role_marker($uid,2,$post);
				$post['who']['data'] = $image.$post['who']['data'];

			}
			if(isset($post['who_2']) && (($class == 'qa-q-view' && qa_opt('marker_plugin_w_qv')) || ($class == 'qa-q-item' && qa_opt('marker_plugin_w_qi')) || ($class == 'qa-a-item' && qa_opt('marker_plugin_w_a')) || ($class == 'qa-c-item' && qa_opt('marker_plugin_w_c')))) {
				$handle = strip_tags($post['who_2']['data']);
				$uid = $this->getuserfromhandle($handle);
				$image = $this->get_role_marker($uid,2,$post);
				$post['who_2']['data'] = $image.$post['who_2']['data'];
			}

			qa_html_theme_base::post_meta($post, $class, $prefix, $separator);
		}

		function ranking_label($item, $class)
		{
			if(qa_opt('marker_plugin_w_users') && $class == 'qa-top-users') {
				$handle = strip_tags($item['label']);
				$uid = $this->getuserfromhandle($handle);
				$image = $this->get_role_marker($uid,2,null);
				$item['label'] = $image.$item['label'];
			}
			qa_html_theme_base::ranking_label($item, $class);
		}

	// worker

		function get_role_marker($uid,$switch,$post)
		{
			require_once QA_INCLUDE_DIR.'qa-app-users.php';
			require_once QA_INCLUDE_DIR.'qa-db-selects.php';

			if (QA_FINAL_EXTERNAL_USERS) {
				$user = get_userdata( $uid );
				if (isset($user->wp_capabilities['administrator']) || isset($user->caps['administrator']) || isset($user->allcaps['administrator'])) {
					$userlevel = QA_USER_LEVEL_ADMIN;
				}
				elseif (isset($user->wp_capabilities['moderator']) || isset($user->caps['moderator'])) {
					$userlevel = QA_USER_LEVEL_MODERATOR;
				}
				elseif (isset($user->wp_capabilities['editor']) || isset($user->caps['editor'])) {
					$userlevel = QA_USER_LEVEL_EDITOR;
				}
				elseif (isset($user->wp_capabilities['contributor']) || isset($user->caps['contributor'])) {
					$userlevel = QA_USER_LEVEL_EXPERT;
				}
				else
					return;
			}
			else {
				$cached_user = qa_db_get_pending_result('user'.$uid, qa_db_user_account_selectspec($uid, true));
				$userlevel = @$cached_user['level'];

			}

			if ($post !== null) {
				$categoryids = explode(',',$post['raw']['categoryids']);

				if (count($categoryids)) {

					$userlevels = qa_db_get_pending_result('user'.$uid.'levels', qa_db_user_levels_selectspec($uid, true));

					$categorylevels=array(); // create a map
					foreach ($userlevels as $ulevel)
						if ($ulevel['entitytype']==QA_ENTITY_CATEGORY)
							$categorylevels[$ulevel['entityid']]=$ulevel['level'];

					foreach ($categoryids as $categoryid)
						$userlevel = max($userlevel, @$categorylevels[$categoryid]);
				}

			}

			if ($userlevel == QA_USER_LEVEL_ADMIN || $userlevel == QA_USER_LEVEL_SUPER)
				$img = 'admin';
			elseif ($userlevel == QA_USER_LEVEL_MODERATOR)
				$img = 'moderator';
			elseif ($userlevel == QA_USER_LEVEL_EDITOR)
				$img = 'editor';
			elseif ($userlevel == QA_USER_LEVEL_EXPERT)
				$img = 'expert';
			else
				return;

			$level = qa_html(qa_user_level_string($userlevel));
			if($switch == 1)
				return '<div class="qa-avatar-marker"><img title="'.$level.'" width="20" src="'.QA_HTML_THEME_LAYER_URLTOROOT.$img.'.png"/></div>';
			else
				return '<span class="qa-who-marker qa-who-marker-'.$img.'" title="'.$level.'">'.qa_opt('marker_plugin_who_text').'</span>';
		}

		function getuserfromhandle($handle)
		{
			require_once QA_INCLUDE_DIR.'qa-app-users.php';

			if (QA_FINAL_EXTERNAL_USERS) {
				$publictouserid=qa_get_userids_from_public(array($handle));
				$userid=@$publictouserid[$handle];

			}
			else {
				$userid = qa_db_read_one_value(
					qa_db_query_sub(
						'SELECT userid FROM ^users WHERE handle = $',
						$handle
					),
					true
				);
			}
			if (!isset($userid)) return;
			return $userid;
		}
	}