<?php
namespace tistory_writer;

/* metabox debug */
TistoryWriter::init();
$api_mgr = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);

global $wp;
$current_url = home_url(add_query_arg(array(), $wp->request));

global $post;
/* POST 방식으로 Metabox 데이터 전달하기 위한 변수 설정 */
$post_visibility   = get_post_meta( $post->ID, '_select_visibility', true );
$post_category     = get_post_meta( $post->ID, '_select_category', true );
$post_protect      = get_post_meta( $post->ID, '_checkProtected', true );
$post_allowcomment = get_post_meta( $post->ID, '_checkAllowComment', true );
$post_tag          = get_post_meta( $post->ID, '_input_tag', true );
$post_link         = get_post_meta( $post->ID, '_postId', true );
$post_switch       = get_post_meta( $post->ID, '_turnIntegratationOff', true );

wp_nonce_field( 'reference_meta_box', 'reference_nonce' );


// 페이지 로드
include "metabox_main.php";
?>