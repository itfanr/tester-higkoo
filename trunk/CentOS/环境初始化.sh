#!/bin/bash
nowTime=`date '+%Y%m%d%H%M%S'`
profileFile="/etc/profile"
inittabFile="/etc/inittab"

# 修改文件描述符大小
cp ${profileFile} ${profileFile}.${nowTime}.bak
echo "备份${profileFile} --> ${profileFile}.${nowTime}.bak"
grep "ulimit" ${profileFile} > /dev/null
if [ $? -eq 1 ]
then
	echo "ulimit -n 81920" >> ${profileFile}
else
	sed -i '/ulimit/'d ${profileFile}
	echo "ulimit -n 81920">> ${profileFile}
	source ${profileFile}
fi

# 开机不启用图形界面
cp ${inittabFile} ${inittabFile}.${nowTime}.bak
echo "备份${inittabFile} --> ${inittabFile}.${nowTime}.bak"
sed -i 's/\(id\):[0-6]/\1:3/g' ${inittabFile}
echo 'Chang ${inittabFile} to "Full multiuser mode", id=3 .'

# 定制系统启动时的服务
chkconfig bluetooth off
chkconfig iptables off
chkconfig ip6tables off
chkconfig sendmail off
chkconfig vncserver on
chkconfig yum-updatesd off
# 安装JDK
setupFile="/data/setupfiles/jdk-6u12-linux-i586.bin"
extractDir="/data/programfiles/"
linkDir="/usr/local/jdk"

cd ${extractDir}
chmod +x ${setupFile}
ls ${extractDir} > /tmp/s.ls
${setupFile}
ls ${extractDir} > /tmp/t.ls
#  通过比较目录来确定解压目录
verName=`comm -13 /tmp/s.ls /tmp/t.ls`
rm -f /tmp/s.ls /tmp.t.ls
programDir="/data/programfiles/"${verName}

ln -s ${programDir} ${linkDir}
# 添加JAVA_HOME到环境变量
cp ${profileFile} ${profileFile}.${nowTime}.bak
echo "备份${profileFile} --> ${profileFile}.${nowTime}.bak"
grep "JAVA_HOME" ${profileFile} > /dev/null
if [ $? -eq 1 ]
then
        echo "JAVA_HOME=/usr/local/jdk" >> ${profileFile}
else
        sed -i '/JAVA_HOME/'d ${profileFile}
        echo "JAVA_HOME=/usr/local/jdk">> ${profileFile}
        source ${profileFile}
fi
# 添加java到Path
which java
if [ $? -eq 1 ]
then
        export PATH=$PATH:$JAVA_HOME/bin
fi
#  修改应用程序的JAVA_HOME=/usr/local/jdk

# 安装Apache
setupFile="/data/setupfiles/httpd-2.2.11.tar.bz2"
extractDir="/data/setupfiles/"
#  使用tar查看解压后的目录名
verName=`tar -jtf ${setupFile} | head -1`
setupDir="/data/setupfiles/"${verName}
programDir="/data/programfiles/"${verName}
linkDir="/usr/local/apache2"

tar -jxvf ${setupFile} -C ${extractDir}
cd ${setupDir}
make clean
./configure --prefix=${programDir} 
make
make install

ln -s ${programDir} ${linkDir}
# 查询运行实例：
ps -A | grep httpd
# 启动方法，任选一种：
/usr/local/apache2/bin/apachectl -k start
/usr/local/apache2/bin/httpd
# 停止，任选一种：
/usr/local/apache2/bin/apachectl -k stop
killall -9 httpd

# 卸载（先停止服务）
#  方法一，在安装过程的make后执行：
make uninstall
#  方法二，直接删除安装目录即可：
rm -f ${linkDir}
rm -rf ${programDir}

# 安装Jboss
setupFile="/data/setupfiles/jboss-4.2.3.GA.zip"
extractDir="/data/programfiles/"
# 使用unzip显示解压后的目录
verName=`unzip -l ${setupFile} | awk '{if(NR==4)print $4}'`
programDir="/data/programfiles/"${verName}
linkDir="/usr/local/jboss"

unzip ${setupFile} -d ${extractDir}
ln -s ${programDir} ${linkDir}

# 查询运行实例
ps -aux | grep jboss
# 启动方法：
/usr/local/jboss/bin/run.sh
# 停止方法，任选一种：
/usr/local/jboss/bin/shutdown.sh
kill -9 `ps aux | grep jboss | cut -c9-14` 2>/dev/null
# 卸载（先停止服务直接删除安装目录即可）：
rm -f ${linkDir}
rm -rf ${programDir}