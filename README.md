[![Travis](https://img.shields.io/travis/rust-lang/rust.svg)]()
[![Jenkins tests](https://img.shields.io/jenkins/t/https/jenkins.qa.ubuntu.com/view/Precise/view/All%20Precise/job/precise-desktop-amd64_default.svg)]()
[![Dockbit](https://img.shields.io/dockbit/DockbitStatus/health.svg?token=TvavttxFHJ4qhnKstDxrvBXM)]()
[![Bower](https://img.shields.io/bower/v/bootstrap.svg)]()
[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)]()


# Description

### 1. jmbits 介绍
   jmbits 是一个基于 nexusphp v1.5beta 版本的二次开发的 pt 系统
### 2. 开发成员
   只有本人一个，惭愧惭愧 ：(
### 3. 开发这个的目的
   * 首先就是为了西北大学能有一个自己的 pt 站，但是我不想直接使用别人的原生的代码，毕竟要有一点自己的特色
   * 为了能有一个真正的 PHP 项目去练手
   * 本来 nexusphp 开放源码的目的就是为了让大家把这份代码发扬一下，但是目前的 pt 圈子大部分都是修改后的代码全部闭源，我觉得并不是一个好现象。想改出来让大家一起改，虽然有的时候恨不得直接用个框架重写 nexusphp 最后还是没能落实，时间有点紧。毕竟已经是大三老狗了。
   
# Change Log

## v0.1.0 (2016/12/22)
* 使用一种灰色的格调完成前端的编写
* 做了大量的修改，并且把个人感觉不比要的功能删除了

## v0.2.0 (2017/2/20)
* 全部重写修改成扁平风格
* 其他的有点多记不清了


# Install 
1. 安装 LAMP 开发环境
2. 导入 db 里面的 sql 文件
3. 修改 config 目录下的配置文件 主要调整数据库  以及发件邮箱等等

# TODO
* 目前的阶段肯定没有 TODO 了，因为当前没有时间
* 有时间了我会考虑使用 python 或者 php 来重写全部的代码
* 添加爬虫自动抓取视频网站的视频并做种
* 把以前写的一个 auto-seed 自动发种机器人添加上，改成图形界面的方便管理人员（auto-seed 是借鉴的北邮人的一位开发者写的，然后做了一些新的功能比如一开始那个代码是不能爬取北邮人的网站的，然后就是自动下载，自动上传，自动做种）
