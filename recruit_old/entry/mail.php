<?php header("Content-Type:text/html;charset=utf-8"); ?>
<?php //error_reporting(E_ALL | E_STRICT);
##-----------------------------------------------------------------------------------------------------------------##
#
#  PHPメールプログラム　フリー版 最終更新日2018/07/27
#　改造や改変は自己責任で行ってください。
#
#  HP: https://www.php-factory.net/
#
#  重要！！サイトでチェックボックスを使用する場合のみですが。。。
#  チェックボックスを使用する場合はinputタグに記述するname属性の値を必ず配列の形にしてください。
#  例　name="当サイトをしったきっかけ[]"  として下さい。
#  nameの値の最後に[と]を付ける。じゃないと複数の値を取得できません！
#
##-----------------------------------------------------------------------------------------------------------------##
if (version_compare(PHP_VERSION, '5.1.0', '>=')) { //PHP5.1.0以上の場合のみタイムゾーンを定義
    date_default_timezone_set('Asia/Tokyo'); //タイムゾーンの設定（日本以外の場合には適宜設定ください）
}
/*-------------------------------------------------------------------------------------------------------------------
* ★以下設定時の注意点　
* ・値（=の後）は数字以外の文字列（一部を除く）はダブルクオーテーション「"」、または「'」で囲んでいます。
* ・これをを外したり削除したりしないでください。後ろのセミコロン「;」も削除しないください。
* ・また先頭に「$」が付いた文字列は変更しないでください。数字の1または0で設定しているものは必ず半角数字で設定下さい。
* ・メールアドレスのname属性の値が「Email」ではない場合、以下必須設定箇所の「$Email」の値も変更下さい。
* ・name属性の値に半角スペースは使用できません。
*以上のことを間違えてしまうとプログラムが動作しなくなりますので注意下さい。
-------------------------------------------------------------------------------------------------------------------*/


//---------------------------　必須設定　必ず設定してください　-----------------------

//サイトのトップページのURL　※デフォルトでは送信完了後に「トップページへ戻る」ボタンが表示されますので
$site_top = "https://maruyoshi-ironworks.com/recruit/";

//管理者のメールアドレス ※メールを受け取るメールアドレス(複数指定する場合は「,」で区切ってください 例 $to = "aa@aa.aa,bb@bb.bb";)
$to = "yamauchi-t@plus-agc.com";

//自動返信メールの送信元メールアドレス
//必ず実在するメールアドレスでかつ出来る限り設置先サイトのドメインと同じドメインのメールアドレスとすることを強く推奨します
$from = "yamauchi-t@plus-agc.com";

//フォームのメールアドレス入力箇所のname属性の値（name="○○"　の○○部分）
$Email = "Eメール";
//---------------------------　必須設定　ここまで　------------------------------------


//---------------------------　セキュリティ、スパム防止のための設定　------------------------------------

//スパム防止のためのリファラチェック（フォーム側とこのファイルが同一ドメインであるかどうかのチェック）(する=1, しない=0)
//※有効にするにはこのファイルとフォームのページが同一ドメイン内にある必要があります
$Referer_check = 0;

//リファラチェックを「する」場合のドメイン ※設置するサイトのドメインを指定して下さい。
//もしこの設定が間違っている場合は送信テストですぐに気付けます。
$Referer_check_domain = "https://maruyoshi-ironworks.com/recruit/";

/*セッションによるワンタイムトークン（CSRF対策、及びスパム防止）(する=1, しない=0)
※ただし、この機能を使う場合は↓の送信確認画面の表示が必須です。（デフォルトではON（1）になっています）
※【重要】ガラケーは機種によってはクッキーが使えないためガラケーの利用も想定してる場合は「0」（OFF）にして下さい（PC、スマホは問題ないです）*/
$useToken = 1;
//---------------------------　セキュリティ、スパム防止のための設定　ここまで　------------------------------------


//---------------------- 任意設定　以下は必要に応じて設定してください ------------------------


// 管理者宛のメールで差出人を送信者のメールアドレスにする(する=1, しない=0)
// する場合は、メール入力欄のname属性の値を「$Email」で指定した値にしてください。
//メーラーなどで返信する場合に便利なので「する」がおすすめです。
$userMail = 1;

// Bccで送るメールアドレス(複数指定する場合は「,」で区切ってください 例 $BccMail = "aa@aa.aa,bb@bb.bb";)
$BccMail = "";

// 管理者宛に送信されるメールのタイトル（件名）
$subject = "エントリーがありました";

// 送信確認画面の表示(する=1, しない=0)
$confirmDsp = 1;

// 送信完了後に自動的に指定のページ(サンクスページなど)に移動する(する=1, しない=0)
// CV率を解析したい場合などはサンクスページを別途用意し、URLをこの下の項目で指定してください。
// 0にすると、デフォルトの送信完了画面が表示されます。
$jumpPage = 1;

// 送信完了後に表示するページURL（上記で1を設定した場合のみ）※httpから始まるURLで指定ください。（相対パスでも基本的には問題ないです）
$thanksPage = "https://maruyoshi-ironworks.com/recruit/thanks.html";

// 必須入力項目を設定する(する=1, しない=0)
$requireCheck = 1;

/* 必須入力項目(入力フォームで指定したname属性の値を指定してください。（上記で1を設定した場合のみ）
値はシングルクォーテーションで囲み、複数の場合はカンマで区切ってください。フォーム側と順番を合わせると良いです。
配列の形「name="○○[]"」の場合には必ず後ろの[]を取ったものを指定して下さい。*/
$require = array('希望職種', 'お名前', 'Eメール', '電話番号');


//----------------------------------------------------------------------
//  自動返信メール設定(START)
//----------------------------------------------------------------------

// 差出人に送信内容確認メール（自動返信メール）を送る(送る=1, 送らない=0)
// 送る場合は、フォーム側のメール入力欄のname属性の値が上記「$Email」で指定した値と同じである必要があります
$remail = 1;

//自動返信メールの送信者欄に表示される名前　※あなたの名前や会社名など（もし自動返信メールの送信者名が文字化けする場合ここは空にしてください）
$refrom_name = "有限会社まるよし工業";

// 差出人に送信確認メールを送る場合のメールのタイトル（上記で1を設定した場合のみ）
$re_subject = "送信ありがとうございました";

//フォーム側の「名前」箇所のname属性の値　※自動返信メールの「○○様」の表示で使用します。
//指定しない、または存在しない場合は、○○様と表示されないだけです。あえて無効にしてもOK
$dsp_name = 'お名前';

//自動返信メールの冒頭の文言 ※日本語部分のみ変更可
$remail_text = <<< TEXT

エントリーありがとうございました。
早急にご返信致しますので今しばらくお待ちください。

送信内容は以下になります。

TEXT;


//自動返信メールに署名（フッター）を表示(する=1, しない=0)※管理者宛にも表示されます。
$mailFooterDsp = 1;

//上記で「1」を選択時に表示する署名（フッター）（FOOTER～FOOTER;の間に記述してください）
$mailSignature = <<< FOOTER

・～・～・～・～・～・～・～・～・～・～・～・～・～・～・～
有限会社　まるよし工業　

〒010-1601
秋田県秋田市向浜1丁目6-6
電話:018-883-0750
FAX:018-883-0720
e-mail： maruyoshikougyo@gmail.com
〇「信頼」「感謝」「感動」を大切にします。
・～・～・～・～・～・～・～・～・～・～・～・～・～・～・～

FOOTER;


//----------------------------------------------------------------------
//  自動返信メール設定(END)
//----------------------------------------------------------------------

//メールアドレスの形式チェックを行うかどうか。(する=1, しない=0)
//※デフォルトは「する」。特に理由がなければ変更しないで下さい。メール入力欄のname属性の値が上記「$Email」で指定した値である必要があります。
$mail_check = 1;

//全角英数字→半角変換を行うかどうか。(する=1, しない=0)
$hankaku = 0;

//全角英数字→半角変換を行う項目のname属性の値（name="○○"の「○○」部分）
//※複数の場合にはカンマで区切って下さい。（上記で「1」を指定した場合のみ有効）
//配列の形「name="○○[]"」の場合には必ず後ろの[]を取ったものを指定して下さい。
$hankaku_array = array('電話番号', '金額');

//-fオプションによるエンベロープFrom（Return-Path）の設定(する=1, しない=0)　
//※宛先不明（間違いなどで存在しないアドレス）の場合に 管理者宛に「Mail Delivery System」から「Undelivered Mail Returned to Sender」というメールが届きます。
//サーバーによっては稀にこの設定が必須の場合もあります。
//設置サーバーでPHPがセーフモードで動作している場合は使用できませんので送信時にエラーが出たりメールが届かない場合は「0」（OFF）として下さい。
$use_envelope = 0;

//機種依存文字の変換
/*たとえば㈱（かっこ株）や①（丸1）、その他特殊な記号や特殊な漢字などは変換できずに「？」と表示されます。それを回避するための機能です。
確認画面表示時に置換処理されます。「変換前の文字」が「変換後の文字」に変換され、送信メール内でも変換された状態で送信されます。（たとえば「㈱」の場合、「（株）」に変換されます）
必要に応じて自由に追加して下さい。ただし、変換前の文字と変換後の文字の順番と数は必ず合わせる必要がありますのでご注意下さい。*/

//変換前の文字
$replaceStr['before'] = array('①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩', '№', '㈲', '㈱', '髙');
//変換後の文字
$replaceStr['after'] = array(
    '(1)',
    '(2)',
    '(3)',
    '(4)',
    '(5)',
    '(6)',
    '(7)',
    '(8)',
    '(9)',
    '(10)',
    'No.',
    '（有）',
    '（株）',
    '高'
);

//------------------------------- 任意設定ここまで ---------------------------------------------


// 以下の変更は知識のある方のみ自己責任でお願いします。

//----------------------------------------------------------------------
//  関数実行、変数初期化
//----------------------------------------------------------------------
//トークンチェック用のセッションスタート
if ($useToken == 1 && $confirmDsp == 1) {
    session_name('PHPMAILFORMSYSTEM');
    session_start();
}
$encode = "UTF-8"; //このファイルの文字コード定義（変更不可）
if (isset($_GET)) {
    $_GET = sanitize($_GET);
} //NULLバイト除去//
if (isset($_POST)) {
    $_POST = sanitize($_POST);
} //NULLバイト除去//
if (isset($_COOKIE)) {
    $_COOKIE = sanitize($_COOKIE);
} //NULLバイト除去//
if ($encode == 'SJIS') {
    $_POST = sjisReplace($_POST, $encode);
} //Shift-JISの場合に誤変換文字の置換実行
$funcRefererCheck = refererCheck($Referer_check, $Referer_check_domain); //リファラチェック実行

//変数初期化
$sendmail   = 0;
$empty_flag = 0;
$post_mail  = '';
$errm       = '';
$header     = '';

if ($requireCheck == 1) {
    $requireResArray = requireCheck($require); //必須チェック実行し返り値を受け取る
    $errm            = $requireResArray['errm'];
    $empty_flag      = $requireResArray['empty_flag'];
}
//メールアドレスチェック
if (empty($errm)) {
    foreach ($_POST as $key => $val) {
        if ($val == "confirm_submit") {
            $sendmail = 1;
        }
        if ($key == $Email) {
            $post_mail = h($val);
        }
        if ($key == $Email && $mail_check == 1 && !empty($val)) {
            if (!checkMail($val)) {
                $errm       .= "<p class=\"error_messe\">【" . $key . "】はメールアドレスの形式が正しくありません。</p>\n";
                $empty_flag = 1;
            }
        }
    }
}

if (($confirmDsp == 0 || $sendmail == 1) && $empty_flag != 1) {

    //トークンチェック（CSRF対策）※確認画面がONの場合のみ実施
    if ($useToken == 1 && $confirmDsp == 1) {
        if (empty($_SESSION['mailform_token']) || ($_SESSION['mailform_token'] !== $_POST['mailform_token'])) {
            exit('ページ遷移が不正です');
        }
        if (isset($_SESSION['mailform_token'])) {
            unset($_SESSION['mailform_token']);
        } //トークン破棄
        if (isset($_POST['mailform_token'])) {
            unset($_POST['mailform_token']);
        } //トークン破棄
    }

    //差出人に届くメールをセット
    if ($remail == 1) {
        $userBody   = mailToUser($_POST, $dsp_name, $remail_text, $mailFooterDsp, $mailSignature, $encode);
        $reheader   = userHeader($refrom_name, $from, $encode);
        $re_subject = "=?iso-2022-jp?B?" . base64_encode(mb_convert_encoding($re_subject, "JIS", $encode)) . "?=";
    }
    //管理者宛に届くメールをセット
    $adminBody = mailToAdmin($_POST, $subject, $mailFooterDsp, $mailSignature, $encode, $confirmDsp);
    $header    = adminHeader($userMail, $post_mail, $BccMail, $to);
    $subject   = "=?iso-2022-jp?B?" . base64_encode(mb_convert_encoding($subject, "JIS", $encode)) . "?=";

    //-fオプションによるエンベロープFrom（Return-Path）の設定(safe_modeがOFFの場合かつ上記設定がONの場合のみ実施)
    if ($use_envelope == 0) {
        mail($to, $subject, $adminBody, $header);
        if ($remail == 1 && !empty($post_mail)) {
            mail($post_mail, $re_subject, $userBody, $reheader);
        }
    } else {
        mail($to, $subject, $adminBody, $header, '-f' . $from);
        if ($remail == 1 && !empty($post_mail)) {
            mail($post_mail, $re_subject, $userBody, $reheader, '-f' . $from);
        }
    }
} else if ($confirmDsp == 1) {

    /*　▼▼▼送信確認画面のレイアウト※編集可　オリジナルのデザインも適用可能▼▼▼　*/
?>
    <!DOCTYPE html>
    <!--[if lt IE 7]>
    <html class="no-js lt-ie10 lt-ie9 lt-ie8 lt-ie7 "> <![endif]-->
    <!--[if IE 7]>
    <html class="no-js lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
    <!--[if IE 8]>
    <html class="no-js lt-ie10 lt-ie9"> <![endif]-->
    <!--[if IE 9]>
    <html class="no-js lt-ie10"> <![endif]-->
    <!--[if gt IE 8]><!-->
    <html lang="ja" class="no-js">
    <!--<![endif]-->

    <head>
        <!-- Google Tag Manager -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-5Z75X54');
        </script>
        <!-- End Google Tag Manager -->
        <!-- Basic Page Needs -->
        <meta charset="utf-8">
        <title>エントリー | 有限会社まるよし工業</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta http-equiv="content-language" content="ja">

        <!-- Mobile Specific Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- Favicons -->
        <link rel="shortcut icon" href="../../images/favicon.ico">

        <!-- FONTS -->
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,400italic,700'>
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Patua+One:100,300,400,400italic,700'>
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Ubuntu:100,400,400italic,500,700'>

        <!-- CSS -->
        <link rel='stylesheet' href='../../css/global.css'>
        <link rel='stylesheet' href='../css/structure.css'>
        <link rel='stylesheet' href='../css/extreme.css'>
        <link rel='stylesheet' href='../css/custom.css'>

    </head>

    <body class="layout-full-width mobile-tb-left no-content-padding header-transparent header-fw minimalist-header sticky-white ab-hide subheader-both-center menu-line-below-80 menuo-right footer-copy-center"><!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5Z75X54" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <!-- Main Theme Wrapper -->
        <div id="Wrapper">
            <!-- Header Wrapper -->
            <div id="Header_wrapper" style="background-image:url(../images/fix/66.jpg);background-repeat:no-repeat; background-position:center; background-size:cover;">
                <!-- Header -->
                <header id="Header">

                    <!-- Header -  Logo and Menu area -->
                    <div id="Top_bar">
                        <div class="container">
                            <div class="column one">
                                <div class="top_bar_left clearfix loading">
                                    <!-- Logo-->
                                    <div class="logo">
                                        <a id="logo" href="/" title="有限会社まるよし工業"><img class="scale-with-grid" src="../images/fix/logo_recruit.svg" alt="有限会社まるよし工業" width="169" height="60">
                                        </a>
                                    </div>
                                    <!-- Main menu-->
                                    <div class="menu_wrapper">
                                        <nav id="menu" class="menu-main-menu-container">
                                            <ul id="menu-main-menu" class="menu">
                                                <li>
                                                    <a href="../index.html"><span>ホーム</span></a>
                                                </li>
                                                <li>
                                                    <a href="../who-we-are.html"><span>アバウト</span></a>
                                                </li>
                                                <li>
                                                    <a href="../culture.html"><span>カルチャー</span></a>
                                                </li>
                                                <li>
                                                    <a href="../interview.html"><span>インタビュー</span></a>
                                                </li>
                                                <li class="current_page_item">
                                                    <a href="../job.html"><span>ジョブリスト</span></a>
                                                </li>
                                            </ul>
                                        </nav>
                                        <a class="responsive-menu-toggle" href="#"><i class="icon-menu"></i></a>
                                    </div>
                                    <!-- Secondary menu area - only for certain pages -->
                                    <div class="secondary_menu_wrapper"></div>
                                    <!-- Banner area - only for certain pages-->
                                    <div class="banner_wrapper"></div>
                                    <!-- Header Searchform area-->
                                    <div class="search_wrapper">
                                        <form method="get" id="searchform" action="#">

                                            <input type="text" class="field" name="s" id="s" placeholder="Enter your search" />
                                            <input type="submit" class="submit flv_disp_none" value="" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <!--Subheader area - only for certain pages -->
                <div id="Subheader">
                    <div class="container">
                        <div class="column one">
                            <h1 class="title animate" data-anim-type="fadeInUp">Confirm</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Content -->
            <div id="Content">
                <div class="content_wrapper clearfix">
                    <div class="sections_group">
                        <div class="entry-content">
                            <div class="section full-width no-margin-h no-margin-v sections_style_0">
                                <div class="section_wrapper clearfix">
                                    <div class="items_group clearfix">
                                        <!-- One Second (1/2) Column -->
                                        <div class="column one-second column_column ">
                                            <div class="column_attr" style=" background-color:#d01855; padding:30px;">
                                                <h3 style="color: #fff; margin: 0;" class="animate" data-anim-type="fadeInLeft">
                                                    確認画面</h3>
                                            </div>
                                        </div>
                                        <!-- One Second (1/2) Column -->
                                        <div class="column one-second column_column ">
                                            <div class="column_attr" style="padding:30px;">
                                                <a class="mfn-link mfn-link-8" ontouchstart="this.classList.toggle('hover');" data-hover="Phasellus" href="javascript:history.back()">
                                                    <span data-hover="Phasellus"><i class="icon-back"></i>戻る</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="section full-width no-margin-h " style="padding-top:120px; padding-bottom:60px; ">
                                <div class="section_wrapper clearfix">
                                    <div class="items_group clearfix">
                                        <!-- One Second (1/2) Column -->
                                        <div class="column one column_column ">
                                            <div class="column_attr" style=" padding:0 8%;">
                                                <hr class="no_line" style="margin: 0 auto 70px;" />
                                                <!-- Form -->
                                                <div class="section sections_style_0 ">
                                                    <div class="section_wrapper clearfix">
                                                        <div class="items_group clearfix">
                                                            <!-- One Sixth (1/6) Column -->
                                                            <div class="column one-sixth column_placeholder">
                                                                <div class="placeholder">
                                                                    &nbsp;
                                                                </div>
                                                            </div>
                                                            <!-- One Third (1/3) Column -->
                                                            <div class="column one-third column_image ">
                                                                <div class="image_frame image_item no_link scale-with-grid no_border">
                                                                    <div class="image_wrapper"><img class="scale-with-grid" src="../images/fix/home_tuning_contact_logo.jpg" alt="" width="108" height="230" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- One Second (1/2) Column -->
                                                            <div class="column one-second column_column ">
                                                                <div class="column_attr" style=" padding:0 13% 0 0;">
                                                                    <hr class="no_line" style="margin: 0 auto 70px;" />
                                                                    <h3 class="animate" data-anim-type="fadeInRight">Confirmation Screen</h3>
                                                                    <hr class="no_line hrmargin_b_30" />
                                                                    <h2 class="animate" data-anim-type="fadeInRight">
                                                                        確認画面</h2>
                                                                    <hr class="no_line" style="margin: 0 auto 55px;" />
                                                                    <div class="image_frame image_item no_link scale-with-grid alignnone no_border">
                                                                        <div class="image_wrapper"><img class="scale-with-grid" src="../../images/home_tuning_sep3.png" alt="" width="193" height="4" />
                                                                        </div>
                                                                    </div>
                                                                    <hr class="no_line" style="margin: 0 auto 50px;" />
                                                                    <h4 class="animate" data-anim-type="fadeInRight">
                                                                        <!-- ▼************ 送信内容表示部　※編集は自己責任で ************ ▼-->
                                                                        <div id="formWrap">
                                                                            <?php if ($empty_flag == 1) { ?>
                                                                                <div align="center">
                                                                                    <h4>
                                                                                        入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
                                                                                    <?php echo $errm; ?><br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <p align="left">
                                                                                    以下の内容で間違いがなければ、「送信する」ボタンを押してください。</p>
                                                                                <form action="<?php echo h($_SERVER['SCRIPT_NAME']); ?>" method="POST">
                                                                                    <table class="formTable">
                                                                                        <?php echo confirmOutput($_POST); //入力内容を表示
                                                                                        ?>
                                                                                    </table>
                                                                                    <p align="center"><input type="hidden" name="mail_set" value="confirm_submit">
                                                                                        <input type="hidden" name="httpReferer" value="<?php echo h($_SERVER['HTTP_REFERER']); ?>">
                                                                                        <input type="submit" value="　送信する　" data-gtm-click="contact">
                                                                                        <input type="button" value="前画面に戻る" onClick="history.back()">
                                                                                    </p>
                                                                                </form>
                                                                            <?php } ?>
                                                                            <!-- /formWrap -->
                                                                            <!-- ▲ *********** 送信内容確認部　※編集は自己責任で ************ ▲-->
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <!-- Page devider -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--                <div class="section full-width no-margin-h no-margin-v sections_style_0">-->
                        <!--                    <div class="section_wrapper clearfix">-->
                        <!--                        <div class="items_group clearfix">-->
                        <!--                            &lt;!&ndash; One Fourth (1/4) Column &ndash;&gt;-->
                        <!--                            <div class="column one-fourth column_placeholder">-->
                        <!--                                <div class="placeholder">-->
                        <!--                                    &nbsp;-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                            &lt;!&ndash; Three Fourth (3/4) Column &ndash;&gt;-->
                        <!--                            <div class="column three-fourth column_hover_color ">-->
                        <!--                                <div class="hover_color" style="background:#f52066;">-->
                        <!--                                    <div class="hover_color_bg" style="background:#d01855;">-->
                        <!--                                        <a href="entry.html">-->
                        <!--                                            <div class="hover_color_wrapper">-->
                        <!--                                                <h3 style="color: #fff; margin: 0; padding: 0 30px; text-align: left;" class="animate" data-anim-type="fadeInLeft">-->
                        <!--                                                    ENTRY <span class="extreme_desc">オフィスエンジニアに応募する</span></h3>-->
                        <!--                                            </div>-->
                        <!--                                        </a>-->
                        <!--                                    </div>-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
                        <!--                    </div>-->
                        <!--                </div>-->
                        <div class="section the_content no_content">
                            <div class="section_wrapper">
                                <div class="the_content_wrapper"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ▲ Headerやその他コンテンツなど　※自由に編集可 ▲-->
            <!-- Footer-->
            <!-- Footer-->
            <footer id="Footer" class="clearfix">
                <!-- Footer copyright-->
                <div class="footer_copy">
                    <div class="container">
                        <div class="column one">
                            <div class="copyright">
                                &copy; 2020 有限会社まるよし工業 - ALL RIGHTS RESERVED
                            </div>
                            <!--Social info area-->
                            <ul class="social"></ul>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- JS -->
            <script src="../../js/jquery-2.1.4.min.js"></script>
            <script src="../../js/mfn.menu.js"></script>
            <script src="../../js/jquery.plugins.js"></script>
            <script src="../../js/jquery.jplayer.min.js"></script>
            <script src="../../js/animations/animations.js"></script>
            <script src="../../js/scripts.js"></script>

            <script>
                jQuery(window).load(function() {
                    var retina = window.devicePixelRatio > 1 ? true : false;
                    if (retina) {
                        var retinaEl = jQuery("#logo img.logo-main");
                        var retinaLogoW = retinaEl.width();
                        var retinaLogoH = retinaEl.height();
                        retinaEl.attr("src", "images/retina_extreme.png").width(retinaLogoW).height(retinaLogoH);
                        var stickyEl = jQuery("#logo img.logo-sticky");
                        var stickyLogoW = stickyEl.width();
                        var stickyLogoH = stickyEl.height();
                        stickyEl.attr("src", "images/retina_extreme.png").width(stickyLogoW).height(stickyLogoH);
                        var mobileEl = jQuery("#logo img.logo-mobile");
                        var mobileLogoW = mobileEl.width();
                        var mobileLogoH = mobileEl.height();
                        mobileEl.attr("src", "images/retina_extreme_m.png").width(mobileLogoW).height(mobileLogoH);
                    }
                });
            </script>

    </body>

    </html>
<?php
    /* ▲▲▲送信確認画面のレイアウト　※オリジナルのデザインも適用可能▲▲▲　*/
}

if (($jumpPage == 0 && $sendmail == 1) || ($jumpPage == 0 && ($confirmDsp == 0 && $sendmail == 0))) {

    /* ▼▼▼送信完了画面のレイアウト　編集可 ※送信完了後に指定のページに移動しない場合のみ表示▼▼▼　*/
?>
    <!DOCTYPE HTML>
    <html lang="ja">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
        <meta name="format-detection" content="telephone=no">
        <title>完了画面</title>
    </head>

    <body>
        <div align="center">
            <?php if ($empty_flag == 1) { ?>
                <h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
                <div style="color:red"><?php echo $errm; ?></div>
                <br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
        </div>
    </body>

    </html>
<?php } else { ?>
    送信ありがとうございました。<br />
    送信は正常に完了しました。<br /><br />
    <a href="<?php echo $site_top; ?>">トップページへ戻る&raquo;</a>
    </div>
    <?php copyright(); ?>
    <!--  CV率を計測する場合ここにAnalyticsコードを貼り付け -->
    </body>

    </html>
<?php
                /* ▲▲▲送信完了画面のレイアウト 編集可 ※送信完了後に指定のページに移動しない場合のみ表示▲▲▲　*/
            }
        } //確認画面無しの場合の表示、指定のページに移動する設定の場合、エラーチェックで問題が無ければ指定ページヘリダイレクト
        else if (($jumpPage == 1 && $sendmail == 1) || $confirmDsp == 0) {
            if ($empty_flag == 1) { ?>
    <div align="center">
        <h4>入力にエラーがあります。下記をご確認の上「戻る」ボタンにて修正をお願い致します。</h4>
        <div style="color:red"><?php echo $errm; ?></div>
        <br /><br /><input type="button" value=" 前画面に戻る " onClick="history.back()">
    </div>
<?php
            } else {
                header("Location: " . $thanksPage);
            }
        }

        // 以下の変更は知識のある方のみ自己責任でお願いします。

        //----------------------------------------------------------------------
        //  関数定義(START)
        //----------------------------------------------------------------------
        function checkMail($str)
        {
            $mailaddress_array = explode('@', $str);
            if (preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-zA-Z]+(\.[!#%&\-_0-9a-zA-Z]+)+$/", "$str") && count($mailaddress_array) == 2) {
                return true;
            } else {
                return false;
            }
        }

        function h($string)
        {
            global $encode;

            return htmlspecialchars($string, ENT_QUOTES, $encode);
        }

        function sanitize($arr)
        {
            if (is_array($arr)) {
                return array_map('sanitize', $arr);
            }

            return str_replace("\0", "", $arr);
        }

        //Shift-JISの場合に誤変換文字の置換関数
        function sjisReplace($arr, $encode)
        {
            foreach ($arr as $key => $val) {
                $key              = str_replace('＼', 'ー', $key);
                $resArray[$key] = $val;
            }

            return $resArray;
        }

        //送信メールにPOSTデータをセットする関数
        function postToMail($arr)
        {
            global $hankaku, $hankaku_array;
            $resArray = '';
            foreach ($arr as $key => $val) {
                $out = '';
                if (is_array($val)) {
                    foreach ($val as $key02 => $item) {
                        //連結項目の処理
                        if (is_array($item)) {
                            $out .= connect2val($item);
                        } else {
                            $out .= $item . ', ';
                        }
                    }
                    $out = rtrim($out, ', ');
                } else {
                    $out = $val;
                } //チェックボックス（配列）追記ここまで
                if (get_magic_quotes_gpc()) {
                    $out = stripslashes($out);
                }

                //全角→半角変換
                if ($hankaku == 1) {
                    $out = zenkaku2hankaku($key, $out, $hankaku_array);
                }
                if ($out != "confirm_submit" && $key != "httpReferer") {
                    $resArray .= "【 " . h($key) . " 】 " . h($out) . "\n";
                }
            }

            return $resArray;
        }

        //確認画面の入力内容出力用関数
        function confirmOutput($arr)
        {
            global $hankaku, $hankaku_array, $useToken, $confirmDsp, $replaceStr;
            $html = '';
            foreach ($arr as $key => $val) {
                $out = '';
                if (is_array($val)) {
                    foreach ($val as $key02 => $item) {
                        //連結項目の処理
                        if (is_array($item)) {
                            $out .= connect2val($item);
                        } else {
                            $out .= $item . ', ';
                        }
                    }
                    $out = rtrim($out, ', ');
                } else {
                    $out = $val;
                } //チェックボックス（配列）追記ここまで
                if (get_magic_quotes_gpc()) {
                    $out = stripslashes($out);
                }
                $out = nl2br(h($out)); //※追記 改行コードを<br>タグに変換
                $key = h($key);
                $out = str_replace($replaceStr['before'], $replaceStr['after'], $out); //機種依存文字の置換処理

                //全角→半角変換
                if ($hankaku == 1) {
                    $out = zenkaku2hankaku($key, $out, $hankaku_array);
                }

                $html .= "<tr><th>" . $key . "</th><td>" . $out;
                $html .= '<input type="hidden" name="' . $key . '" value="' . str_replace(array(
                    "<br />",
                    "<br>"
                ), "", $out) . '" />';
                $html .= "</td></tr>\n";
            }
            //トークンをセット
            if ($useToken == 1 && $confirmDsp == 1) {
                $token                      = sha1(uniqid(mt_rand(), true));
                $_SESSION['mailform_token'] = $token;
                $html                       .= '<input type="hidden" name="mailform_token" value="' . $token . '" />';
            }

            return $html;
        }

        //全角→半角変換
        function zenkaku2hankaku($key, $out, $hankaku_array)
        {
            global $encode;
            if (is_array($hankaku_array) && function_exists('mb_convert_kana')) {
                foreach ($hankaku_array as $hankaku_array_val) {
                    if ($key == $hankaku_array_val) {
                        $out = mb_convert_kana($out, 'a', $encode);
                    }
                }
            }

            return $out;
        }

        //配列連結の処理
        function connect2val($arr)
        {
            $out = '';
            foreach ($arr as $key => $val) {
                if ($key === 0 || $val == '') { //配列が未記入（0）、または内容が空のの場合には連結文字を付加しない（型まで調べる必要あり）
                    $key = '';
                } elseif (strpos($key, "円") !== false && $val != '' && preg_match("/^[0-9]+$/", $val)) {
                    $val = number_format($val); //金額の場合には3桁ごとにカンマを追加
                }
                $out .= $val . $key;
            }

            return $out;
        }

        //管理者宛送信メールヘッダ
        function adminHeader($userMail, $post_mail, $BccMail, $to)
        {
            $header = '';
            if ($userMail == 1 && !empty($post_mail)) {
                $header = "From: $post_mail\n";
                if ($BccMail != '') {
                    $header .= "Bcc: $BccMail\n";
                }
                $header .= "Reply-To: " . $post_mail . "\n";
            } else {
                if ($BccMail != '') {
                    $header = "Bcc: $BccMail\n";
                }
                $header .= "Reply-To: " . $to . "\n";
            }
            $header .= "Content-Type:text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();

            return $header;
        }

        //管理者宛送信メールボディ
        function mailToAdmin($arr, $subject, $mailFooterDsp, $mailSignature, $encode, $confirmDsp)
        {
            $adminBody = " $subject \n";
            $adminBody .= "＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
            $adminBody .= postToMail($arr); //POSTデータを関数からセット
            $adminBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n";
            $adminBody .= "送信された日時：" . date("Y/m/d (D) H:i:s", time()) . "\n";
            $adminBody .= "送信者のIPアドレス：" . @$_SERVER["REMOTE_ADDR"] . "\n";
            $adminBody .= "送信者のホスト名：" . getHostByAddr(getenv('REMOTE_ADDR')) . "\n";
            if ($confirmDsp != 1) {
                $adminBody .= "問い合わせのページURL：" . @$_SERVER['HTTP_REFERER'] . "\n";
            } else {
                $adminBody .= "問い合わせのページURL：" . @$arr['httpReferer'] . "\n";
            }
            if ($mailFooterDsp == 1) {
                $adminBody .= $mailSignature;
            }

            return mb_convert_encoding($adminBody, "JIS", $encode);
        }

        //ユーザ宛送信メールヘッダ
        function userHeader($refrom_name, $to, $encode)
        {
            $reheader = "From: ";
            if (!empty($refrom_name)) {
                $default_internal_encode = mb_internal_encoding();
                if ($default_internal_encode != $encode) {
                    mb_internal_encoding($encode);
                }
                $reheader .= mb_encode_mimeheader($refrom_name) . " <" . $to . ">\nReply-To: " . $to;
            } else {
                $reheader .= "$to\nReply-To: " . $to;
            }
            $reheader .= "\nContent-Type: text/plain;charset=iso-2022-jp\nX-Mailer: PHP/" . phpversion();

            return $reheader;
        }

        //ユーザ宛送信メールボディ
        function mailToUser($arr, $dsp_name, $remail_text, $mailFooterDsp, $mailSignature, $encode)
        {
            $userBody = '';
            if (isset($arr[$dsp_name])) {
                $userBody = h($arr[$dsp_name]) . " 様\n";
            }
            $userBody .= $remail_text;
            $userBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
            $userBody .= postToMail($arr); //POSTデータを関数からセット
            $userBody .= "\n＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝\n\n";
            $userBody .= "送信日時：" . date("Y/m/d (D) H:i:s", time()) . "\n";
            if ($mailFooterDsp == 1) {
                $userBody .= $mailSignature;
            }

            return mb_convert_encoding($userBody, "JIS", $encode);
        }

        //必須チェック関数
        function requireCheck($require)
        {
            $res['errm']       = '';
            $res['empty_flag'] = 0;
            foreach ($require as $requireVal) {
                $existsFalg = '';
                foreach ($_POST as $key => $val) {
                    if ($key == $requireVal) {

                        //連結指定の項目（配列）のための必須チェック
                        if (is_array($val)) {
                            $connectEmpty = 0;
                            foreach ($val as $kk => $vv) {
                                if (is_array($vv)) {
                                    foreach ($vv as $kk02 => $vv02) {
                                        if ($vv02 == '') {
                                            $connectEmpty++;
                                        }
                                    }
                                }
                            }
                            if ($connectEmpty > 0) {
                                $res['errm']       .= "<p class=\"error_messe\">【" . h($key) . "】は必須項目です。</p>\n";
                                $res['empty_flag'] = 1;
                            }
                        } //デフォルト必須チェック
                        elseif ($val == '') {
                            $res['errm']       .= "<p class=\"error_messe\">【" . h($key) . "】は必須項目です。</p>\n";
                            $res['empty_flag'] = 1;
                        }

                        $existsFalg = 1;
                        break;
                    }
                }
                if ($existsFalg != 1) {
                    $res['errm']       .= "<p class=\"error_messe\">【" . $requireVal . "】が未選択です。</p>\n";
                    $res['empty_flag'] = 1;
                }
            }

            return $res;
        }

        //リファラチェック
        function refererCheck($Referer_check, $Referer_check_domain)
        {
            if ($Referer_check == 1 && !empty($Referer_check_domain)) {
                if (strpos($_SERVER['HTTP_REFERER'], $Referer_check_domain) === false) {
                    return exit('<p align="center">リファラチェックエラー。フォームページのドメインとこのファイルのドメインが一致しません</p>');
                }
            }
        }

        function copyright()
        {
            echo '<a style="display:block;text-align:center;margin:15px 0;font-size:11px;color:#aaa;text-decoration:none" href="https://www.php-factory.net/" target="_blank">- PHP工房 -</a>';
        }

        //----------------------------------------------------------------------
        //  関数定義(END)
        //----------------------------------------------------------------------
?>