# A fb-auth integration tutorial for NAS

A quick study and fb-auth integration with my DS918+.

![demo.jpeg](images/demo.jpeg)

## Prerequisites
- DS918+ Disk Station
- WebStation Package
- PHP 7.0 Package (with openssl.so module enabled)
- Facebook Application app_id and app_secret

## Installation
1. Clone this project into your workspace <br/>
git clone https://github.com/bxxxjxxg/dsm-fb-auth or <br/>
wget https://github.com/bxxxjxxg/dsm-fb-auth/archive/V1.0.tar.gz && tar zxvf V1.0.tar.gz 

2. Create https://your-website/fb-login/ portal<br/>
cp dsm-fb-auth/etc/nginx/conf.d/www.fb-login.conf /etc/nginx/conf.d/ <br/>
mkdir /var/services/web/fb-login && chmod 755 /var/services/web/fb-login/ <br/>
\# Restart your WebStation package to take effect <br/><br/>
\# For verifcation, <br/>
\# 1) you can check whether /run/php-fpm/php70-fpm.sock exists or not. <br/>
\# 2) you can put a hello-world index.php and try to browse https://your-website/fb-login/

3. Download Facebook PHP SDK into fb-login/ <br/>
wget https://github.com/facebook/php-graph-sdk/archive/5.6.1.tar.gz <br/>
tar zxvf 5.6.1.tar.gz <br/>
mv php-graph-sdk/src /var/services/web/fb-login/ <br/>

4. Copy source code into fb-login/ <br/>
cp dsm-db-auth/var/services/web/fb-login/* /var/services/web/fb-login/

5. Fill the configuration into force <br/>
vim /var/services/web/fb-login/config.php <br/>

6. Finish. Browse https://your-website/fb-login/ and make sure it works. <br/>
\# You should also publish your Facebook application. Then other people can authenicate them afterwards.

## FAQ
1. I see the "file_get_contents errors with https wrapper". How to deal with it? <br/>
Please enable openssl.so for PHP 7.0 and try again.

## Reference
- Facebook Dev Center: https://developers.facebook.com/
- Facebook PHP SDK: https://developers.facebook.com/docs/reference/php/
- Synology Dev Center: https://www.synology.com/zh-tw/support/developer
- facebook/php-graph-sdk: https://github.com/facebook/php-graph-sdk
- Entry Layout CSS: https://codepen.io/russbeye/pen/MYeroq
