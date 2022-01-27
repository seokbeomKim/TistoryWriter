<link href="markdown.css" rel="stylesheet"></link>

# Tistory Writer (Deprecated)
* 현재는 지원하지 않습니다. *
워드프레스용 티스토리 연동 플러그인 입니다.
사용 중 발견되는 버그는 <sukbeom.kim@gmail.com>으로 메일 주시기 바랍니다.

## 요구사양
* Wordpress 버전 4.6 이상
* PHP 5.6 이상

## 기능
포스팅하고자 하는 대상 블로그의 카테고리를 선택하여 글 등록/수정을 할 수 있습니다.

* 연동 대상 블로그 설정
* 카테고리 선택
* 공개 여부 설정
* 태그 설정
* 댓글 허용 권한 설정

## 설치
워드프레스 플러그인의 설치 방법은 2가지 방법이 있습니다.

### 워드프레스 관리페이지 이용
워드프레스의 관리페이지의 플러그인 관리자에서 'Tistory Writer'를 검색하여 설치합니다.

### 직접 설치
아래는 관리페이지를 사용하지 못하거나 워드프레스의 공식 저장소에 올라오지 않은 베타 기능을 테스트하기 위한 목적으로 설치하는 방법입니다. 개발 소스코드는 본 GitHub 저장소에 우선적으로 업데이트됩니다. 

#### 소스 받기
다음 명령어를 통해 코드를 다운로드하거나 본 페이지의 'Download ZIP' 버튼을 클릭합니다.

```
git clone https://github.com/seokbeomKim/TistoryWriter.git
```


#### 파일 복사
1. 워드프레스가 설치된 경로 아래, <b>wp-content\plugins</b> 디렉토리 내에 TistoryWriter 이름으로 <b>Plugin</b> 디렉토리를 복사합니다.

1. 플러그인 파일을 복사한 후 플러그인을 활성화하면 설정 메뉴에 플러그인 메뉴가 추가된 것을 확인할 수 있습니다.


#### 계정 설정
1. 티스토리 API 클라이언트 관리 페이지 (<a href=http://www.tistory.com/guide/api/manage/register>http://www.tistory.com/guide/api/manage/register</a>) 에서 아래와 같이 클라이언트를 등록합니다. (Callback 경로, 서비스 URL은 플러그인 설정 창에서 직접 확인할 수 있습니다.)<br/>
<p align="center">
	<img src="https://github.com/seokbeomKim/TistoryWriter/blob/asset/step1.PNG?raw=true" width="500px">
</p>

2. 1단계에서 등록한 클라이언트 정보를 2단계 입력 창에 입력해준 뒤 'ID정보 저장' 버튼을 클릭합니다.<br/>
<p align="center">
	<img src="https://github.com/seokbeomKim/TistoryWriter/blob/asset/step2.PNG?raw=true" width="500px">
</p>

3. 저장이 완료되면 계정 연동 버튼을 클릭하여 설정을 완료합니다. 설정이 완료되면 AccessCode가 표시되며 해당 코드는 일정 시간이 지나면 만료됩니다. 만료 시에는 다시 갱신되어야 하며, 글 작성 창에서 만료 여부 확인이 가능합니다.<br/>

<p align="center">
	<img src="https://github.com/seokbeomKim/TistoryWriter/blob/asset/step3.PNG?raw=true" width="500px">
</p>

### 글 올리기
1. Post -> Add New 버튼을 클릭하면, 아래와 같이 티스토리 연동 위젯이 추가된 것을 확인할 수 있습니다.<br/>
<p align="center">
	<img src="https://github.com/seokbeomKim/TistoryWriter/blob/asset/step4.PNG?raw=true" width="500px">
</p>

2. 워드프레스에서 Publish 버튼을 클릭하면, 티스토리 내에도 글이 함께 포스팅되는 것을 확인할 수 있습니다.<br/>

### 포스팅 옵션
<p align="center">
	<img src="https://github.com/seokbeomKim/TistoryWriter/blob/asset/step6.PNG?raw=true" width="500px">
</p>

- 연동 기능: 새 글을 워드프레스에 업로드할 때 임시로 티스토리와의 연동 기능을 끄고 업로드 할 수 있습니다.
- 연동 계정: 현재 연동되어 있는 티스토리 계정 아이디가 표시됩니다.
- 포스팅 주소: 워드프레스와 이미 연동되어 있는 글을 편집할 경우, 티스토리 블로그 내의 글 주소가 나타납니다.
- 분류 선택: 티스토리 블로그에 등록된 분류(카테고리)를 선택합니다.
- 공개 여부: 포스팅하는 글의 공개 여부를 선택합니다.
- 태그: 포스팅하는 글의 태그를 입력합니다.


## 만든이
김석범 (<sukbeom.kim@gmail.com>)

## 라이센스
The MIT License

Copyright (c) 2018 Sukbeom Kim

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
