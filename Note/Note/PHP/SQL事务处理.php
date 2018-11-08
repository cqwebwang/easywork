//php多sql的事务
$sqlArr=array();
$sqlArr[]=array(
    "sql"=>"delete from BarLoc_CallLog where ID=:ID or DealTime>0 or IsPlay='否' or AddTime<UNIX_TIMESTAMP(NOW())-300",
    "para"=>array(":id"=>123),
    'rownum'=>1,'msg'=>'失败[L01]'
),
try{//事务（里面有回滚，避免回滚错误，不要写其它逻辑）
    DataHelper::Ins()->open(); 
    DataHelper::Ins()->db->beginTransaction();
    foreach ($sqlArr as $sql_item){
        $pre= DataHelper::Ins()->db->prepare($sql_item["sql"]);
        $ExtRes=$pre->execute($sql_item["para"]); 
        if($ExtRes===false||$pre->rowCount()<$sql_item["rownum"])throw new Exception($sql_item["msg"]);
    }
    if(DataHelper::Ins()->db->commit()) {
        $result["success"]=true;
    }
}catch(Exception $err){
    DataHelper::Ins()->db->rollBack();
    throw $err;
}