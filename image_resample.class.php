<?php
// Imaging
class ImageResample
{

	// Variables
	private $img_input;
	private $img_output;
	private $img_src;
	private $format;
	private $quality = 80;
	private $x_input;
	private $y_input;
	private $x_output;
	private $y_output;
	private $resize;

	public function __construct()
	{
		$this->img_src = null;
		$this->img_input = null;
		$this->img_output = null;
	}
	
	// Set image
	public function setImage($img, $type)
	{
		if(is_file($img))
		{
			$this->format = $type;
			$this->img_src = $img;
				
			// JPEG image
			if($this->format == IMAGETYPE_JPEG)
			{
				$this->img_input = imagecreatefromjpeg($img);
			}
				
			// PNG image
			elseif($this->format == IMAGETYPE_PNG)
			{
				$this->img_input = imagecreatefrompng($img);
			}

			// GIF image
			elseif($this->format == IMAGETYPE_GIF)
			{
				$this->img_input = imagecreatefromgif($img);
			}

		}

		// Get dimensions
		$this->x_input = imagesx($this->img_input);
		$this->y_input = imagesy($this->img_input);
	}

	// Set maximum image size (pixels)
	public function setSize($size = 100)
	{
		// Resize
		if($this->x_input > $size && $this->y_input > $size)
		{
			// Wide
			if($this->x_input >= $this->y_input)
			{
				$this->x_output = $size;
				$this->y_output = ($this->x_output / $this->x_input) * $this->y_input;
			}
			// Tall
			else
			{
				$this->y_output = $size;
				$this->x_output = ($this->y_output / $this->y_input) * $this->x_input;
			}
			// Ready
			$this->resize = TRUE;
		}
		// Don't resize
		else { $this->resize = FALSE; }
	}

	// Set image quality (JPEG only)
	public function setQuality($quality)
	{
		if(is_int($quality))
		{
			$this->quality = $quality;
		}
	}

	// Save image
	public function saveImage($path)
	{
		// Resize
		if($this->resize)
		{
			$this->img_output = imagecreatetruecolor($this->x_output, $this->y_output);
			imagecopyresampled($this->img_output, $this->img_input, 0, 0, 0, 0, $this->x_output, $this->y_output, $this->x_input, $this->y_input);
		}

		// Save JPEG
		if($this->format == IMAGETYPE_JPEG)
		{
			if($this->resize) { $response = imagejpeg($this->img_output, $path, $this->quality); }
			else { $response = copy($this->img_src, $path); }
		}
		// Save PNG
		elseif($this->format == IMAGETYPE_PNG)
		{
			if($this->resize) { $response = imagepng($this->img_output, $path); }
			else { $response = copy($this->img_src, $path); }
		}
		// Save GIF
		elseif($this->format == IMAGETYPE_GIF)
		{
			if($this->resize) { $response = imagegif($this->img_output, $path); }
			else { $response = copy($this->img_src, $path); }
		}

		return $response;
	}
	
	public function haveImageSource()
	{
		return ($this->img_src !== null);
	}

	// Get width
	public function getWidth()
	{
		return $this->x_input;
	}

	// Get height
	public function getHeight()
	{
		return $this->y_input;
	}

	// Clear image cache
	public function clearCache()
	{
		@imagedestroy($this->img_input);
		@imagedestroy($this->img_output);
	}
}
?>