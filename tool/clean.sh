# 清理日志&数据文件

# 每天运行一次

basepath=$(cd `dirname $0`; pwd)

cd $basepath

# 清理日志
cd ../log/
find ./ -type f  -mtime +7 | grep -v gitignore | xargs -n1 -I{} rm -rf {}

# 清理数据文件
cd ../data/
find ./ -type f  -mtime +7 | grep -v gitignore | xargs -n1 -I{} rm -rf {}
