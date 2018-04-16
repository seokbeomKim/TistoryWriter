=== Tistory Writer ===
Contributors: Sukbeom Kim
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GF6ZFVWQ4U3PU
Tags: content, tistory, development, integration
Requires at least: 1.5
Tested up to: 4.1.1
Stable tag: 1.0.1
License: MIT License
License URI: https://mit-license.org/

티스토리 계정을 연동하여 작성한 글을 워드프레스, 티스토리에 포스팅할 수 있도록 도와주는 플러그인입니다.

== Description ==

글을 포스팅할 때 발행 단계에서 티스토리 블로그의 카테고리를 선택할 수 있습니다.

= 지원 기능 =
포스팅하고자 하는 대상 블로그의 카테고리를 선택하여 글 등록/수정을 할 수 있습니다.

* 카테고리 선택
* 공개 여부 설정
* 태그 설정
* 댓글 허용 권한 설정

== Installation ==
다음은 플러그인 설치법입니다. 
단, 워드프레스의 플러그인 설치 기능을 이용하시는 분들께서는 관리 페이지에서 설치하시면 됩니다.

1. `/wp-content/plugins/` 디렉토리에 TistoryWriter 압축을 해제합니다.
1. 워드프레스의 Plugin 메뉴를 선택하여 플러그인을 활성화합니다.
1. 추가한 Tistory Writer를 클릭하고 티스토리 계정을 연동합니다.
1. 연동이 완료되면 글 작성 시마다 등록할 블로그의 카테고리 선택버튼이 활성화됩니다.
1. 설정 창에 있는 단계를 따라 티스토리 API에 등록합니다.

== Screenshots ==
1. 플러그인 설정 창은 '설정' 메뉴 밑에서 확인할 수 있습니다.
2. 연동을 위해서 http://www.tistory.com/guide/api/manage/register 에서 서비스를 등록합니다.
3. 등록한 정보를 설정합니다.
4. 계정 연동 버튼을 클릭하여 연동을 완료합니다.
5. 포스팅 글을 올릴 때 연동이 되어 있는 경우, 연동 계정에 대한 정보가 나타나고 티스토리에 올릴 글의 속성을 설정할 수 있습니다.
6. 블로그에 글이 자동으로 업로드 된 것을 확인할 수 있습니다.

== Changelog ==
= 1.0.1 =
* 티스토리 연동 문제 수정 

= 1.0.0 =
* 기본 기능 구현 및 최초 배포