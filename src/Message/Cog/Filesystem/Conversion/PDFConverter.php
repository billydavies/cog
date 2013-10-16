<?php

namespace Message\Cog\Filesystem\Conversion;

use InvalidArgumentException;

use Knp\Snappy\Pdf;
use Message\Cog\Filesystem\File;

class PDFConverter extends AbstractConverter {

	public function generate($path, $html)
	{
		$ext = pathinfo($path, PATHINFO_EXTENSION);

		if (null === $ext) {
			$path .= ".ext";
		}
		elseif ("pdf" !== $ext) {
			throw new InvalidArgumentException(sprintf("Your destination path must have a .pdf extension for conversion, '%s' passed.", $ext));
		}

		$pdf = new Pdf;
		$pdf->setBinary($this->_getBinDir() . 'wkhtmltopdf-' . $this->_getBinaryType());

		foreach ($this->_options as $key => $value) {
			$pdf->setOption($key, $value);
		}

		$pdf->generateFromHTML($html, $path);

		return new File($path);
	}

}