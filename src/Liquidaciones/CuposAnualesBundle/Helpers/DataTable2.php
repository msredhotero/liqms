<?php
namespace Liquidaciones\CuposAnualesBundle\Helpers;

/**
 * Description of DataTable
 *
 * @author rmoviglia
 */
class DataTable2 {
    //put your code here
    
    public $strColumns;
    public $strOrder;
    public $strFilters;
    public $strQuery;
    public $strRowsFiltered;
    
    public function __construct($arrayColumnas){
        
        $strColumns = $arrayColumnas;
        $this->setColumns($strColumns);
        $this->setFilters();
        $this->setOrder();
        return null;
        
    }
    
    public function setColumns($columnas){
        $this->strColumns = $columnas;
    }
    
    public function setQuery($sql){
     
        /* DB table to use */
        
        $intStart   = intval($_GET['iDisplayStart']);
        $intStop    = $intStart + intval($_GET['iDisplayLength']);
        
        $old_query = $sql;
        
        
        //$query = $sql." AND rownum >= ".$intStart." AND rownum <= ".$intStop." ".$this->strFilters.$this->strOrder;
        
        $sq1 = $sql.$this->strFilters.$this->strOrder;
        
        $query = "SELECT * FROM ( SELECT rownum runm, (SELECT COUNT(*) FROM ($old_query)) as Total, tabla.* FROM (".$sq1.") tabla ) WHERE runm >= ".$intStart." AND runm <= ".$intStop;
        
        $this->strQuery = $query;
        
    }
    
    public function setOrder(){
        
        $sOrder = "";
        
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $this->strColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".$_GET['sSortDir_'.$i] .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
        
        $this->strOrder = $sOrder;
        
    }
    
    public function setFilters(){
        
        $sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($this->strColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
			{
				$sWhere .= $this->strColumns[$i]." LIKE '%". $_GET['sSearch'] ."%' OR ";
			}
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($this->strColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $this->strColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
		}
	}
        
        $this->strFilters = $sWhere;
        
    }
    
    public function buildDataTable($rResultTotal, $count_parcial, $count_total){
        
        //var_dump($rResultTotal);
        /*
        $output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => count($rResultTotal),
		"iTotalDisplayRecords" => $rResultTotal[0]["TOTAL"],
		"aaData" => array()
	);
        */
        
        $output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $count_total,
		"iTotalDisplayRecords" => $count_parcial,
		"aaData" => array()
	);
        
        
        foreach($rResultTotal as $aRow)
	//while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($this->strColumns) ; $i++ )
		{
			/* General output */
			$row[] =  $aRow[ $this->strColumns[$i] ];
		}   
                /*
                $row[] =  " <div class='rowControl' style='display: block;'>
                                <a href='#' class='linkVer'>Ver</a>
                                <a href='#' class='linkEditar'>Editar</a>
                            </div>";
                */
                $row[] =  '<div class="btn-group">
                                <button class="btn btn-ba" type="button">Acciones</button>

                                        <button class="btn btn-ba dropdown-toggle" data-toggle="dropdown" type="button">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a href="#" class="linkVer">Ingresar</a>
                                                </li>
                                            </ul>
			</div>';
                
		$output['aaData'][] = $row;
	}
	
	//echo json_encode( $output );
        return json_encode($output);  
        
    }
            
}
