#!/bin/sh
set -e
for PEAR_PKG in Auth_SASL HTTP_Client Mail_Mime XML_Tree
do
        PKG_TEMP=$(mktemp -d)
        cd ${PKG_TEMP}
        pear download ${PEAR_PKG}
        dh-make-pear ${PEAR_PKG}*.tgz
        cd $(find -mindepth 1 -type d | head -n1)
        dpkg-buildpackage -uc -us -rfakeroot
        sudo dpkg -i ../*.deb
        rm -rf ${PKG_TEMP}
done
