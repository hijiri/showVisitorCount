/**
 * Loggix_Plugin - Show Visitor Count
 *
 * @copyright Copyright (C) UP!
 * @author    hijiri
 * @link      http://tkns.homelinux.net/
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @since     2010.04.24
 * @version   10.5.27
 */

●訪問者数とオンラインユーザ数を表示するプラグイン

■概略
このソフトウェアは、Loggixを使用したサイトの訪問者数とオンラインユーザ数を表示するプラグインです。

■詳細
訪問者数とオンラインユーザ数を表示するだけの極々シンプルなカウンターです。

同日内の同一IPアドレスからは1度しかカウントアップしません。指定した時間内に有ったアクセスをオンラインユーザとして扱います。

■インストール/アンインストール方法
インストール
    1./plugins/へshowVisitorCount.phpをアップロードします。必要であれば、return しているHTMLを修正してください。
    2./data/へshowVisitorCount.dbをアップロードし、読み書き出来るパーミッションに変更します。
    3.<?php echo showVisitorCount();?>をテンプレートファイルの表示したい個所へ追加します。
アンインストール
    1./plugins/からshowVisitorCount.phpを削除します。
    2./data/からshowVisitorCount.dbを削除します。
    3.<?php echo showVisitorCount();?>をテンプレートファイルから削除します。

■使用方法
カウンター数値は1からスタートします。任意の数値からスタートしたい場合は、showVisitorCount.dbに対して以下のsqliteコマンドを実行してください。

UPDATE count_log SET count = '任意の数値';

■その他
このプラグインは、/plugins/へ配置すれば自動で適用される通常の動作とは違い、<?php echo showVisitorCount();?>を記入したテンプレートファイルが読み込まれる度に動作します。

したがって、Loggix内の全ての訪問者とオンラインユーザをカウントする場合は、/theme/base.htmlへ<?php echo showVisitorCount();?>を追加するのが良いでしょう。任意のページのみをカウントする場合は、それに対応したテンプレートファイルに追加してください。

■サポート
作者多忙の為サポート出来ません。意見/感想はContactからご連絡ください。

■更新履歴
2010-05-27:細かいバグ修正
2010-05-09:公開
