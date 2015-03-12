<?php
class Picture
{
	private static function Zoom($oldImg,$newImg,$imgInfo,$maxWidth=200,$maxHeight=200)
	{
		if( $maxWidth > $imgInfo[0] || $maxHeight > $imgInfo[1] )
		{
			$maxWidth = $imgInfo[0];

			$maxHeight = $imgInfo[1];
		}
		else
		{
			if ( $imgInfo[0] < $imgInfo[1] )
				$maxWidth = ($maxHeight / $imgInfo[1]) * $imgInfo[0];
			else
				$maxHeight = ($maxWidth / $imgInfo[0]) * $imgInfo[1];
		}

		$image_p = imagecreatetruecolor($maxWidth, $maxHeight);

		switch($imgInfo[2])
		{
			case 1:
				$image = imagecreatefromgif($oldImg);
				break;
			case 2:
				$image = imagecreatefromjpeg($oldImg);
				break;
			case 3:
				$image = imagecreatefrompng($oldImg);
			break;
		}

		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $maxWidth, $maxHeight, $imgInfo[0], $imgInfo[1]);

		imagejpeg($image_p, $newImg,100);

		imagedestroy($image_p);

		imagedestroy($image);
	}

	private static function Save($imgInfo,$saveImage)
	{
		if( $imgInfo[0] > 200 || $imgInfo[1] > 200 )
		{
			self::Zoom($saveImage."_b",$saveImage."_s",$imgInfo,200,200);
		}
		else
		{
			copy($saveImage."_b",$saveImage."_s");
		}

		if( $imgInfo[0] > 1000 || $imgInfo[1] > 1000 )
		{
			self::Zoom($saveImage."_b",$saveImage."_b",$imgInfo,1000,1000);
		}
	}

	public static function Upload($file,$data,$uid)
	{
		$savePath = date('Y/n/j');

		mkDirs("./pictures/".$savePath);

		$saveName = $savePath."/".date('His')."_".$uid."_".rand(1,32000);

		$saveImage = "./pictures/".$saveName;

		if( $file != "" )
		{
			if( $file['size'] > 2097152 )
			{
				return array( "error" => "图片太大" );
			}

			$imgInfo = @getimagesize($file['tmp_name']);

			if( !$imgInfo || !in_array($imgInfo[2], array(1,2,3)) )
			{
				return array( "error" => "图片文件格式不被接受" );
			}

			if( $imgInfo[0] < 100 || $imgInfo[1] < 100 )
			{
				return array( "error" => "图片尺寸不能小于 100 x 100" );
			}

			if( @move_uploaded_file($file['tmp_name'], $saveImage."_b") )
			{
				self::Save($imgInfo,$saveImage);

				return array( "error" => "", "name" => $saveName );
			}
		}

		return array( "error" => "图片上传失败" );
	}

	public static function Delete($picture)
	{
		if( $picture != "" )
		{
			unlink("./pictures/".$picture."_b");

			unlink("./pictures/".$picture."_s");
		}	
	}
}
?>