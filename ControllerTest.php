<?php
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    private $model;
    private $view;
    private $sut;
    
    public function setUp() :void {
        $d = new YahtzeeDice();
        $this->model = new Yahtzee($d);
        $this->view = $this->createStub(YahtzeeView::class);
        $this->sut = new YahtzeeController($this->model, $this->view);
    }

	
	/////////////////////////////////////////////////////////////////////////
	
	/**
	* @covers YahtzeeController::get_model()
	*/
	public function test_get_model()
	{	//set up (done in fixture)
		
		//call
		$result=$this->sut->get_model();
		
		//check
		$this->assertInstanceOf(Yahtzee::class,$result);
	}
	
	/**
	* @covers YahtzeeController::get_view()
	*/
	public function test_get_view()
	{	//set up (done in fixture)
		
		//call
		$result=$this->sut->get_view();
		
		//check
		$this->assertInstanceOf(YahtzeeView::class,$result);
	}
	
	/**
	* @covers YahtzeeController::get_possible_categories()
	*/
	public function test_get_possible_categories()
	{	//set up stub to manipulate get_kept_dice() & get_scorecard()
		$stub = $this->createStub(Yahtzee::class);
		$myRoll=array(1,2,3,4,5);
		$myCard=array(
			"ones" => NULL,
			"twos" => NULL,
			"threes" => NULL,
			"fours" => NULL,
			"fives" => NULL,
			"sixes" => NULL,
			"three_of_a_kind" => NULL,
			"four_of_a_kind" => NULL,
			"full_house" => NULL,
			"small_straight" => NULL,
			"large_straight" => NULL,
			"chance" => NULL,
			"yahtzee" => NULL
		);
		$stub->method('get_kept_dice')->willReturn($myRoll);
		$stub->method('get_scorecard')->willReturn($myCard);
		$this->sut = new YahtzeeController($stub, $this->view);
		
		//Configure the stub/call
		
		$result=$this->sut->get_possible_categories();
		
		$expected = array(
			"ones" => 1,
			"twos" => 2,
			"threes" => 3,
			"fours" => 4,
			"fives" => 5,
			"sixes" => 0,
			"three_of_a_kind" => 0,
			"four_of_a_kind" => 0,
			"full_house" => 0,
			"small_straight" => 30,
			"large_straight" => 40,
			"chance" => 15,
			"yahtzee" => 0
		);
		
		//check
		$this->assertEquals($expected, $result);
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////
//	
////	   public function test_process_score_input()
////		{	//set up (done in fixture)
////			
////	   }


    /**
	* @covers YahtzeeController::process_score_input()
	*/
	public function test_process_score_input_EXIT()
	{	//set up (done in fixture)
		$expected=-1;
        
        //call&check
		$result=$this->sut->process_score_input("exit");
        $this->assertEquals($expected, $result);
		
		$result=$this->sut->process_score_input("q");
        $this->assertEquals($expected, $result);
    }
	
////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	* @covers YahtzeeController::process_keep_input()
	*/
	public function test_process_keep_input()
	{	//setup
		$d = new YahtzeeDice();
		$sut = new Yahtzee($d);
		$roll=$sut->roll(5);
		$expected=array($roll[0],$roll[2],$roll[4]);
		$sut->keep_by_index("0 2 4");
		
		$this->assertEquals($sut->get_kept_dice(), $expected);
	}
	

	/**
	* @covers YahtzeeController::process_keep_input()
	*/
	public function test_process_keep_input_exit() {
        //set up (done in fixture)
        $expected=-1;
        
        //call&check
        $result = $this->sut->process_keep_input("exit");
        $this->assertEquals($expected, $result);
        
        $result = $this->sut->process_keep_input("q");
        $this->assertEquals($expected, $result);
    }
    

	/**
	* @covers YahtzeeController::process_keep_input()
	*/
	public function test_process_keep_input_none() {
        //set up (done in fixture)
        $expected=-2;
        
        //call&check
        $result = $this->sut->process_keep_input("none");
        $this->assertEquals($expected, $result);
        
        $result = $this->sut->process_keep_input("pass");
        $this->assertEquals($expected, $result);
        
        $result = $this->sut->process_keep_input("");
        $this->assertEquals($expected, $result);
    }
    

	/**
	* @covers YahtzeeController::process_keep_input()
	*/
	public function test_process_keep_input_all() {
        //set up (done in fixture)
        $expected=0;
        
        //call&check
        $this->sut->get_model()->roll(5);
        $result = $this->sut->process_keep_input("all");
        $this->assertEquals($expected, $result);
        $this->assertEquals(5,count($this->sut->get_model()->get_kept_dice()));
    }
	
	/**
	* @covers YahtzeeController::process_keep_input()
	*/
	public function test_process_keep_input_error() {
        //set up (done in fixture)
        $expected=-2;
        
        //call&check
        $this->sut->get_model()->roll(5);
        $result = $this->sut->process_keep_input("Quack");
        $this->assertEquals($expected, $result);

        //call&check
        $this->sut->get_model()->roll(5);
        $result = $this->sut->process_keep_input("4 7");
        $this->assertEquals($expected, $result);

    }
}