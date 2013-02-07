%#function network
%db="hgf"
%dbuser="hgf"
%dbpwd="hgf@user2012"
%jdbcdriver="com.mysql.jdbc.Driver"
%jdbcpath="jdbc:mysql://localhost:3306/hgf"
%inputspath="./uploads/"
%outputspath="./results/"

inputs = fileread('configs.txt');
fr = regexp(inputs,'db="(?<name>.*?)"','names' );
db = fr.name;
fr = regexp(inputs,'dbuser="(?<name>.*?)"','names' );
dbuser = fr.name;
fr = regexp(inputs,'dbpwd="(?<name>.*?)"','names' );
dbpwd = fr.name;
fr = regexp(inputs,'jdbcdriver="(?<name>.*?)"','names' );
jdbcdriver = fr.name;
fr = regexp(inputs,'jdbcpath="(?<name>.*?)"','names' );
jdbcpath = fr.name;
fr = regexp(inputs,'inputspath="(?<name>.*?)"','names' );
inputspath = fr.name;
fr = regexp(inputs,'outputspath="(?<name>.*?)"','names' );
outputspath = fr.name;

load dadosrede_210711
javaaddpath('mysql-connector-java-5.0.8-bin.jar');
while (1)
    dbid = database(db,dbuser,dbpwd,jdbcdriver,jdbcpath);
    var = exec(dbid, 'select * from task where status="submitted"');
    b = fetch(var);
    result= b.data;
    if ~(strcmp('No Data',result{1}))
        l = 1;
		filein = result(l, 1);
		id = result(l, 3);
		exec(dbid, ['update task set status="executing" where id=' num2str(id{1})]);
%         try
            disp(['Executing ' filein]);
            inputfastaUnique = fastaread([inputspath filein{1}]);
            %une contigs em multifasta
            if length(inputfastaUnique)>1
                inputfastaUnique = unectsy(inputfastaUnique);
            end
            retfas = hgf(inputfastaUnique,dadosrede_210711);
            ponto = find(filein{1}=='.');
            arqout = ['hgf_' filein{1}(1:ponto(end)) 'gbk'];
            printgbk2 (retfas,[outputspath arqout]);
            exec(dbid, ['update task set fileout="' arqout '", status="completed" where id=' num2str(id{1})]);
            disp([filein ' completed']);
        catch
            exec(dbid, ['update task set status="error" where id=' num2str(id{1})]);
            disp('Error');
        end
    end
    close (dbid);
    pause(2);
end
