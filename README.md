# haruca - Half Ability Router Utility and Config Archiver

## これはなに？
>Cisco IOSを対象にした機器ログ自動収集ツール＆CLIによるユーティリティを提供します。~
もちろんcisco機器のコンフィグ管理として利用できます。~
cacti用プラグインとして動作するため単独での動作は不可です。~
windows版cactiでは動作不可です。~

## なにができる？
- 定期ログの取得と閲覧
    - 対象機器に定期的にtelnet/sshログインしてログを取得
    - 対象機器はcactiに登録されているもの全て
    - 機器をカテゴリわけし、パスワードを一括管理
    - ログの種類は任意に設定可能
    - ログは世代管理可能（差分 or 毎回、任意に設定可能）
    - 取得ログはホスト名などから検索し閲覧可能

- 機器のステータスチェック
- 独自スクリプトにより対象機器にログインして条件にあう結果を一覧で表示させることができます
    - コンフィグ設定変更ホストの履歴一覧表示
        - 定期ログ取得の show startup-config を世代管理し前回取得時と異なる場合に表示させます
    - 機器設定の保存漏れチェック
        - startup-config/running-config に差分がある場合は、保存漏れとしてリストアップします

- コンフィグ／パスワード一括変更
    - 指定したホストに対して同じ設定を投入
    - パスワードはカテゴリごとに全台の設定を変更
        - con 0とvty 0 4のみ対応
        - vty5 15は設定としては変更できますが、データベース上には保管されません。

- CLIスクリプトの提供
    - 自動telnet/sshし指定したコマンドを発行（自動ログ取得のバックエンド処理スクリプト）
    - 自動取得したコンフィグの表示や検索（全ての設定内容から特定文字列を抜き出す、とか）
    - 取得ログの整形表示（show arp 結果のMACアドレスからベンダ名への変換、とか）

- その他ツール
    - 機種、IOSバージョン、シリアル番号の一覧の生成
    - ワイルドカード計算スクリプト（ipcalc.pl利用）
    - 伝送時間計算（オーバヘッド考慮しない）

- snmpトラップ受信（おまけ）
    - トラップタイプは標準のもの限定（非標準は手動での追加）
    - pingによる死活監視を実施し、擬似トラップとして表示
    - trap受信時にアラートを送信（SMTPサーバを利用したメールおよびIPMessenger）


## require



## 開発環境
- CentOS Linux release 7.7.1908 
- cacti-1.2.8
- ciscoルータエミュレータ（GNS3 Cisco2621×４台構成）
- ciscoルータ実機（Cisco841M,Cisco1812J）

## 注意事項
- 個人による開発を行っています。
- 利用は自己責任でお願いします。
- 開発はciscoIOSのエミュレーション環境を利用しているため、実機とは異なる可能性があります。


## Clone , permission , software
```
cd /usr/share/cacti/plugins
git clone https://github.com/bashaway/haruca

chown apache.apache ./haruca/bin/conffile

dnf -y install gcc zip perl-CPAN perl-DBI
cpan -i Net::SSH::Expect Net::Telnet
```
