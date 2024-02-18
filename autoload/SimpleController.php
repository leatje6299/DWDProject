<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class SimpleController
{
	private $mapper;

	public function __construct($table)
	{
		global $f3;						// needed for $f3->get()
		$this->mapper = new DB\SQL\Mapper($f3->get('DB'), $table);	// create DB query mapper object
	}

	public function setNewUser($data)
	{
		$this->mapper->username = $data["username"];
		$this->mapper->password = $data["password"];
		$this->mapper->save();					 // save new record with these fields
	}

	public function getData()
	{
		return $this->mapper->find();
	}

	public function getThirdplaceData($f3, $type)
	{
        $db = $f3->get('DB');
        if(empty($type))
        {
            $result = $db->exec('SELECT thirdplaces.*, COUNT(notes.id) as note_count FROM thirdplaces LEFT JOIN notes ON thirdplaces.id = notes.thirdplace_id GROUP BY thirdplaces.id');
            return $result;
        }
        $result = $db->exec('SELECT thirdplaces.*, COUNT(notes.id) as note_count FROM thirdplaces LEFT JOIN notes ON thirdplaces.id = notes.thirdplace_id WHERE type = :type GROUP BY thirdplaces.id', array(':type' => $type));
        return $result;
	}

	public function insertNote($reason, $thirdplace_id, $user_id)
	{
		$this->mapper->reason = $reason;
		$this->mapper->thirdplace_id = $thirdplace_id;
		$this->mapper->user_id = $user_id;
		$this->mapper->save();
	}

	public function insertThirdplace($name, $position_x, $position_y, $type)
	{
		$this->mapper->name = $name;
		$this->mapper->position_x = $position_x;
		$this->mapper->position_y = $position_y;
		$this->mapper->type = $type;
		$this->mapper->save();
	}

	public function getUserId($f3, $username)
	{
		$db = $f3->get('DB');

		// Prepare the SQL statement
		$result = $db->exec("SELECT id FROM users WHERE username = ?", $username);

		// Return the user_id, or null if no user was found
		return $result ? $result[0]['id'] : null;
	}

	public function getThirdplaceByName($f3, $thirdplace)
	{
		$db = $f3->get('DB');

		$result = $db->exec("SELECT id, position_x, position_y FROM thirdplaces WHERE name = ?", $thirdplace);

		return $result ? $result[0] : null;

	}

	public function getNotesByUser($f3, $user_id)
	{
		$db = $f3->get('DB');
		$notes = $db->exec('SELECT notes.*, thirdplaces.name AS thirdplace_name FROM notes 
                         LEFT JOIN thirdplaces ON notes.thirdplace_id = thirdplaces.id 
                         WHERE notes.user_id = ?', $user_id);
		return $notes;
	}

	public function getNotesByThirdplace($f3, $thirdplace_name)
	{
        $db = $f3->get('DB');
        $notes = $db->exec("SELECT notes.* FROM notes
                        LEFT JOIN thirdplaces ON notes.thirdplace_id = thirdplaces.id
                        WHERE thirdplaces.name = ?", $thirdplace_name);
        return $notes;
    }

	public function getUserHint($str)
	{
		$list = $this->mapper->find(["name LIKE ?", "%" . $str . "%"]);
		$hint = "";
		foreach ($list as $obj) {
			$arr = $this->mapper->cast($obj);
			if ($hint == "")
				$hint = $arr["name"];
			else
				$hint .= ", " . $arr["name"];
		}

		return $hint;
	}

	public function deleteFromDatabase($idToDelete)
	{
		$this->mapper->load(['id=?', $idToDelete]); // load DB record matching the given ID
		$this->mapper->erase();						// delete the DB record
	}

	public function loginUser($user, $pwd)
	{
        $userRecord = $this->mapper->load(array('username=?', $user));
        if ($userRecord) {
            $hashedPassword = $userRecord->password;
            return password_verify($pwd, $hashedPassword);
        } else {
            // No user record found
            return false;
        }
	}
}