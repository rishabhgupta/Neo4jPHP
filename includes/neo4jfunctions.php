<!--place to store all neo4j functions-->

<?php
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
use Everyman\Neo4j\Client,
	    Everyman\Neo4j\Transport,
	    Everyman\Neo4j\Node,
	    Everyman\Neo4j\Relationship;

require('vendor/autoload.php'); 
// Connecting to the default port 7474 on localhost

	/*
	*function:nodeExists to find whether the node exists or not
	*@param $label of the node, $key and $value and optional $where for further filteration
	*@return false if not exists, node if exists
	*/
	function nodeExists($label,$key,$value,$where="")
	{
		$client = new Everyman\Neo4j\Client();
		$queryString = 'MATCH (n:'.$label.' {'.$key.': "'.$value.'"}) '.$where.' return n;';
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
		$resultSet = $query->getResultSet();
		if (count($resultSet)==0)
		{
			return false;
		}
		else
		{
			foreach ($resultSet as $row) 
			{
				
			    return $row[0];

			}
		}
	}

	/*
	*function:relExists to find whether a relation exists between to nodes
	*@param $labelFrom,$keyFrom,$valueFrom of the from node, $labelTo, $keyTo and $valueTo of the To node.
	*$rel relationship lebel between the two nodes.
	*@return false if not exists, true if exists
	*/
	function relExists($labelFrom,$labelTo,$keyFrom,$valueFrom,$rel,$keyTo,$valueTo,$where="")
	{
		$client = new Everyman\Neo4j\Client();
		$queryString = 'MATCH (n:'.$labelFrom.' {'.$keyFrom.': "'.$valueFrom.'"})-[:'.$rel.']->(m:'.$labelTo.' {'.$keyTo.':"'.$valueTo.'"}) '.$where.' return count(*)';
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
		$resultSet = $query->getResultSet();
		if ($resultSet>=1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	*function:createStudent_Node to create Student node with properties if not exists
	*@param $id: Reg No, $fullname: Full Name, $deg: Degree, $course: Course, $yoj: year of joiing of Student
	*/
	function createStudent_Node($id,$fullname,$deg,$course,$yoj)
	{
		$client = new Everyman\Neo4j\Client();
		if (!nodeExists('Student','sid',$id))
		{
			//node does not exist, create node
			echo 'need to create';
			
			$user = new Node($client);
		    $user->setProperty('sid', $id)->save();
		    $user->setProperty('name', $fullname)->save();
		    $user->setProperty('degree',$deg)->save();
		    $user->setProperty('course',$course)->save();
		    $user->setProperty('yoj',$yoj)->save();
		    $userLabel = $client->makeLabel('Student');
			$user->save()->addLabels(array($userLabel));
			echo 'Node Created';
			
		}
		else
		{
			//node exist, do nothing
		}

	}

	/*
	*function:createRoom_Node creates Room Node if not exists.
	*@param $hostelName: name of the hostel block, $roomno: room number
	*/
	function createRoom_Node($hostelName,$roomno)
	{
		$client = new Everyman\Neo4j\Client();
		$room = new Node($client);
		$room->setProperty('no',$roomno)->save();
		$room->setProperty('block',$hostelName)->save();
		$roomLabel = $client->makeLabel('Room');
		$room->save()->addLabels(array($roomLabel));
		echo '<br> Room Created';
	}

	/*
	*function:createStudentRoom_rel to create Relationship between two nodes if node exists
	*@param $id of the Student, $hostelName and $roomno of the hostel
	*/
	function createStudentRoom_rel($id,$hostelName,$roomno)
	{

		//check if hostel room node exists
		$where = 'WHERE n.block = "'.$hostelName.'"';
		if(!nodeExists('Room','no',$roomno,$where))
		{
			createRoom_Node($hostelName,$roomno);
		}
		else
		{
			//room exists, do nothing
		}
		//get the two nodes
		$student=nodeExists('Student','sid',$id);
		$room = nodeExists('Room','no',$roomno,$where);
		//cheak if relationship between the two nodes exists already if it does not then create it
		$where2 = 'WHERE m.block = "'.$hostelName.'"';
		if(!relExists('Student','Room','sid',$id,'STAYS_IN','no','443',$where2))
		{
			//relationship does not exists and needs to be created
			$student->relateTo($room, 'STAYS_IN')->save();
		}
		else
		{
			//reltionship exists, do nothing
		}
		
	}
	
?>