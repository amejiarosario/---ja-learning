<?php

/**
 * This is the model class for table "tbl_tutorials".
 *
 * The followings are the available columns in table 'tbl_tutorials':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $link
 * @property string $accessed
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property Chapters[] $chapters
 * @property Users $user
 */
class Tutorial extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Tutorial the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_tutorials';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, name, link, accessed, created_at', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('name, link', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, name, link, accessed, created_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'chapters' => array(self::HAS_MANY, 'Chapters', 'tutorial_id'),
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'name' => 'Name',
			'link' => 'Link',
			'accessed' => 'Accessed',
			'created_at' => 'Created At',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('accessed',$this->accessed,true);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Tutorial website crawler. Looks for tutorials' chapters
	 */
	private function tutorialWebCrawler($website)
	{
		$test = "tutorialWebCrawler " . $website ;
		echo Yii::trace(CVarDumper::dumpAsString($test),'vardump');
	
	}	
	
	
	/**
	 * Save tutorial and its chapters (doing web crawling)
	 */
	public function save()
	{
		$this->tutorialWebCrawler($this->link);
		parent::save();
	}
	
	

}