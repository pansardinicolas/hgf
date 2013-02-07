count=`ps -ef | grep run_hgf.sh | wc -l`
if [ $count -gt 3 ]
then 
   echo "Already running $count threads..."
else 
   count=`mysql hgf -uhgf -pbioinfo2012 -N -e "select * from task where status='submitted';" | wc -l`
   if [ $count -gt 0 ]
   then
      echo "Started HGF "`date`
      ./run_hgf.sh /usr/local/MATLAB/MATLAB_Compiler_Runtime/v717
      echo "Done HGF "`date`
   fi
fi
