<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Openbuildings\Spiderling\Page;
use Openbuildings\Spiderling\Exception_Notfound;
use Openbuildings\Spiderling\Exception_Curl;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;

class GenerateByLinkController extends Controller
{
	public function getFormGenTests()
	{
		return view('admin.test.getTestFromLink');
	}
	//Tạo ra từng bài kiểm tra bằng link:
	public function generateTest(Request $request)
	{
		$request->validate([
			'link'=>'required',
		]);
		$this->getTest($request['link']);
		\Alert::success(trans('done'))->flash();
		return redirect()->route('crud.test.index');
	}
	//Tạo ra nhiều bài kiểm tra bằng link
	public function generateTests(Request $request)
	{
		$request->validate([
			'link'=>'required',
		]);
		$this->getTestsOnFirst($request['link']);
		\Alert::success(trans('done'))->flash();
		return redirect()->route('crud.test.index');
	}
     //Lấy dữ liệu ở trang đầu tiên
	public function getTestsOnFirst($link)
	{
        //Đưa link về trang đầu tiên của tài liệu
		$link =  preg_replace('/page=[\d]+.*?/', '', $link);
		$page = new Page();
        //Thử xem có vô được trang hay không
		try {
			$page->visit($link);
		} catch (Exception_Curl $e) {
			\Alert::error(trans($e->getMessage()))->flash();
			return false;
		}
		try {
			$div = $page->find('div.view-content');
		} catch (Exception_Notfound $e) {
			\Alert::error(trans('backpack::crud.zeroRecords'))->flash();
			return false;
		}
       //Kiểm tra xem có phân trang hay không
       //Phân trang có thể lấy được trang cuối hay không
		try {
			$liLast = $page->find('ul > li.last');
			preg_match('/page=[\d]+.*?/', $liLast->html(), $pageLast);
			if (!empty($pageLast)) {
				$last = (int)preg_replace('/\D/', '', $pageLast[0]);
				$i = 2;
			}
		} catch (Exception_Notfound $e) {
			\Alert::error(trans('undefinded last page'))->flash();
		}
 //xóa các ký tự \n và blank line
		$content = trim(preg_replace('/\s\s+/', ' ', $div->html()));
		$content = str_replace("\n","",$content);
		while (true) {
                  //lấy href từ các thẻ a
			preg_match('/<div\b[^>]*><a\b[^>]*>(.*?)<\/div>/', $content, $aTag );
         //Nếu lấy hết thẻ a của trang
			if (empty($aTag)) {
				if (isset($last)) {
					while ($i <= $last) {
						if(strpos($link, 'page=')){
							$link =  preg_replace('/page=[\d]+.*?/', 'page='.$i++, $link);
						} else{
							$link .= '?page='.$i++;
						}
						$this->getTestsPerPage($link);
					}
				}
				\Alert::success(trans('done'))->flash();
				return true;
			}
			preg_match('/href=".*?"/', $aTag[0], $href);
			$href[0] =  str_replace("href","",$href[0]);
			$href[0] =  str_replace('"',"",$href[0]);
			$destination =  str_replace('=',"",$href[0]);
			$domain = parse_url($link, PHP_URL_HOST);
			if (!strpos($destination,$domain)) {
				$destination = parse_url($link, PHP_URL_HOST) . $destination;
			}
   //     //Nối vào chuỗi đích
			// dd($destination);
       // dd($destination);

			$this->getTest($destination);
			$content = str_replace($aTag[0],"",$content);
		}
	}
//Lấy dữ liệu ở các trang tiếp theo
	public function getTestsPerPage($link)
	{
		$page = new Page();
    //Thử xem có vào được trang không
		try {
			$page->visit($link);
		} catch (Exception_Curl $e) {
			\Alert::error(trans($e->getMessage()))->flash();
			return false;
		}
 //Thử xem có lấy được nội dung của trang không
		try {
			$div = $page->find('div.view-content');
		} catch (Exception_Notfound $e) {
			\Alert::error(trans('backpack::crud.zeroRecords'))->flash();
			return false;
		}
 //xóa các ký tự \n và blank line
		$content = trim(preg_replace('/\s\s+/', ' ', $div->html()));
		$content = str_replace("\n","",$content);
		while (true) {
                  //lấy href từ các thẻ a
			preg_match('/<div\b[^>]*><a\b[^>]*>(.*?)<\/div>/', $content, $aTag );
         //Nếu lấy hết thẻ a của trang
			if (empty($aTag)) {
				return true;
			}
			preg_match('/href=".*?"/', $aTag[0], $href);
			$href[0] =  str_replace("href","",$href[0]);
			$href[0] =  str_replace('"',"",$href[0]);
			$destination =  str_replace('=',"",$href[0]);
       //Nối vào chuỗi đích
			$destination = parse_url($link, PHP_URL_HOST) . $destination;
       // dd($destination);

			$this->getTest($destination);
			$content = str_replace($aTag[0],"",$content);
		}
	}
//Lấy các câu hỏi và đáp án
	public function getTest($link)
	{
		$page = new Page();
		try {
			$page->visit($link);
		} catch (Exception_Curl $e) {
			\Alert::error(trans($e->getMessage()))->flash();
			return false;
		}
		try {
			$div = $page->find('div.tex2jax');
		} catch (Exception_Notfound $e) {
			\Alert::error(trans('backpack::crud.zeroRecords'))->flash();
			return false;
		}
        //Tạo ra bài kiểm tra mới
		$title = $page->find('h1');
		$title = $title->html();
		$title = preg_replace("/<.*?>/", "", $title);
		$test = Test::create(['title'=>$title]);
        //xóa các ký tự \n và blank line
		$content = trim(preg_replace('/\s\s+/', ' ', $div->html()));
		$content = str_replace("\n","",$content);
		while (true) {
			preg_match('/<p\b[^>]*><strong>(.*?)<\/p>/', $content, $question );
            //Nếu không còn câu hỏi nào sẽ kết thúc
			if(empty($question)){
				break;
			}
			$ques = $question[0];
            //Kiểm tra đã hết nội dung câu hỏi hay chưa
			$possion = strpos( $content, $question[0] )+strlen($question[0]);
			$p = $content[$possion].$content[$possion+1].$content[$possion+2];
			$content = str_replace($question[0],"",$content);
			while($p == '<p>'){
				preg_match('/<p>(.*?)<\/p>/', $content, $question );
				$ques .= $question[0];
				$possion = strpos( $content, $question[0] )+strlen($question[0]);
				$p = $content[$possion].$content[$possion+1].$content[$possion+2];
				$content = str_replace($question[0],"",$content);
			}
            //Nếu hết nội dung câu hỏi thì thêm câu hỏi vào db
			$ques = preg_replace("/<.*?>/", "", $ques);
			$ques = preg_replace('/Câu [0-9]+:/', "", $ques);
			$question = $test->questions()->create(['content' => $ques]);
            //Lấy câu trả lời cho câu hỏi
			preg_match('/<ul>(.*?)<\/ul>/', $content, $answer);
            // dd($answer);
			while(true){
                //Lọc ra câu trả lời
				preg_match('/<li>(.*?)<\/li>/', $answer[0], $ans);
                //Kiểm tra đã hết nội dung câu trả lời hay chưa
				if(empty($ans)){
					break;
				}
				$an = $ans[1];
				$is_true = strpos($an, 'h6')?true:false;
                //Bỏ A. B. C. đi
				$an = preg_replace("/<.*?>/", "", $an);
				$an = substr($an, 1);
				$an = substr($an, 1);
				$an = trim(preg_replace('/s+/', ' ', $an));
                //Nếu hết nội dung câu trả lời thì thêm câu hỏi vào db
				if($is_true){
					$question->answers()->create(['content'=>$an,'is_true'=>true]);
				}else{
					$question->answers()->create(['content'=>$an]);
				}
                //Xóa câu trả lời đã thêm vào db
				$answer[0] = str_replace($ans[0],"",$answer[0]);
                // dd($answer[0]);
			}
            //Xóa câu trả lời đi tiếp tục vòng lặp
			$content = str_replace($answer[0],"",$content);
		}
		\Alert::success(trans('backpack::crud.insert_success'))->flash();
		return true;
	}
}
