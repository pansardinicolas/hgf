#!/bin/bash

# $1 = diretório com os arquivos de upload
# deleta todos os arquivos armazenados a mais de 1 dia
# e deleta do banco de dados

# $2 = diretório com os arquivos de result
# delete todos os arquivos armazenados a mais de 1 dia
echo "Started Findold "`date`
for x in $(find ./$1/* -mtime +1); do
	# deleta o arquivo do diretório $1
	rm -f $x;
	echo "	Oldest file in "$1" is "$x;
	# encontra os ids mais antigos para deletar
	oldest=$(mysql -u hgf -pbioinfo2012 --skip-column-names -e \
	"SELECT MIN(id) FROM task" hgf);
	removed=$(mysql -u hgf -pbioinfo2012 -e \
	"SELECT filein FROM task WHERE id='$oldest'" hgf);
	# imprime o id e o nome do arquivo a ser removido do bd
	echo "	Oldest file in bd "$removed;
	echo "	id: "$oldest;
	# remove do banco
	ans=$(mysql -u hgf -pbioinfo2012 -e "DELETE FROM task WHERE id='$oldest'" hgf);
	echo $ans;
done;

for i in $(find ./$2/* -mtime +1); do
	# deleta o arquivo do diretório $2
	rm -f $i;
	echo "	Oldest file in "$2" is "$i;
done;
echo "Done findold "`date`
