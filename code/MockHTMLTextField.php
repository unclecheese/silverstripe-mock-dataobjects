<?php




class MockHTMLTextField extends DataExtension {

	public function getFakeData(Generator $faker) {
		$paragraphs = rand(1, 5);
		$i = 0;
		$ret = "";
		while($i < $paragraphs) {
			$ret .= "<p>".$faker->paragraph(rand(2,6))."</p>";
			$i++;
		}
		return $ret;
	}

}
