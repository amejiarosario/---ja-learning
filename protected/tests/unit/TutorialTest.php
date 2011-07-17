<?php

class TutorialTest extends CDbTestCase
{
	public function testCRUD()
	{
		$time = date('Y-m-d H:i:s');
		$name = 'Stella Test';
		$link = 'http://stella.se.rit.edu/tests/';
		
		//echo $time;
		
		//-----------
		// test Create
		//-----------
		$tut = new Tutorial;
		$tut->setAttributes(array(
			'user_id' => 1,
			'name' => $name,
			'link' => $link,
			'accessed' => '1986-01-01 00:00:00',
			'created_at' => $time,
		));
		//$tut->save();
		$this->assertTrue($tut->save());
		
		//-----------
		// test Read
		//-----------
		$rtut = Tutorial::model()->findByPk($tut->id);
		$this->assertTrue($rtut instanceof Tutorial);
		$this->assertEquals($rtut->name, $name);
		$this->assertEquals($rtut->link, $link);
		$this->assertEquals($rtut->created_at, $time);
		
		//-----------
		// test Update
		//-----------
		$updated = 'updated'.$name;
		$tut->name = $updated;
		$this->assertTrue($tut->save());
		// test update
		$rtut = Tutorial::model()->findByPk($tut->id);
		$this->assertTrue($rtut instanceof Tutorial);
		$this->assertEquals($rtut->name, $updated);
		$this->assertEquals($rtut->link, $link);
		$this->assertEquals($rtut->created_at, $time);
		
		
		//-----------
		// test Chapter were created
		//-----------	
		
		// TODO
		

		//*
		//-----------
		// test Delete
		//-----------	
		$id = $tut->id;
		$this->assertTrue($tut->delete());
		$deleted = Tutorial::model()->findByPk($id);
		$this->assertEquals(NULL,$deleted);
		//*/
		
		
		
	}
}
	
?>