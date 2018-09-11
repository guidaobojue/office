<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Per;
class ExcelController extends Controller {
	protected $cache_url ;
	public function __construct($param = true){
		parent::__construct();
		A("Permission")->check();
	}
	public function index(){
	
	}

	// 导出报表
    public function export($titles, $datas)
    {

        include LIB_PATH.'/Org/Util/PHPExcel/Classes/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();

        array_unshift($datas, $titles);

        // A-Z数组
        $cols = range('A', 'Z');
        // 格式化日期
        // $time_format = array('time');
        // set data
        foreach ($datas as $n => $data) {
            $cols_num = 0;
            foreach ($titles as $k => $title) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$cols_num].($n+1), $data[$k]);
                $cols_num++;
            }
        }


        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="倒休信息.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $re=$objWriter->save('php://output');
		var_dump($re);
    }



	public function getExcel(){
		include LIB_PATH.'/Org/PHPExcel/PHPExcel.class.php';
		$PHPExcel = new \PHPExcel();

		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数
		$highestColumm= PHPExcel_Cell::columnIndexFromString($colsNum); //字母列转换为数字列 如:AA变为27

		/** 循环读取每个单元格的数据 */
		for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
			for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
				$columnName = PHPExcel_Cell::stringFromColumnIndex($column);
				echo $columnName.$row.":".$sheet->getCellByColumnAndRow($column, $row)->getValue()."<br />";
			}
		}
	}

	public function excel2003xml(){
		/** Error reporting */
		error_reporting(E_ALL);

		date_default_timezone_set('Europe/London');

		/** PHPExcel_IOFactory */
		require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';


		echo date('H:i:s') , " Load from Excel2003XML file" , PHP_EOL;
		$callStartTime = microtime(true);

		$objReader = PHPExcel_IOFactory::createReader('Excel2003XML');
		$objPHPExcel = $objReader->load("Excel2003XMLTest.xml");


		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		echo 'Call time to read Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , PHP_EOL;
		// Echo memory usage
		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , PHP_EOL;


		echo date('H:i:s') , " Write to Excel5 format" , PHP_EOL;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
		echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', __FILE__) , PHP_EOL;


		// Echo memory peak usage
		echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , PHP_EOL;

		// Echo done
		echo date('H:i:s') , " Done writing file" , PHP_EOL;

	}







	public function isAllow(){
	
	}

	public function install(){
		$config = array(
			"controller_name" => "Excel模块",
			"author" => "matengfei",
		);
		parent::install($config);
	}

	public function getModulePer(){
		$per = array(
			"a" => array(
				"pName"=>"a",   //方法名
				"pInfo" =>"输出a", //方法描述
			),
			"b" => array(
				"pName"=>"b",
				"pInfo" =>"输出b",
			),
			"c" => array(
				"pName"=>"c",
				"pInfo" =>"输出c",
			),
		);
		return array(parent::getModuleId() => $per);
	}
	public function uninstall(){

	}

	public function getRole(){
		$rs = parent::getRole();
		return $rs;
	}


	public function genNav(){
	}

}
