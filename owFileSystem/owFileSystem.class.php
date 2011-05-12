<?php
class owFileSystem {

	/**
	 *
	 * supprime tous les fichiers anciens de plus $nbOfDays jours
	 * 
	 * @param array $fileList
	 * @param int $nbOfDays
	 * 
	 * @throws Exception
	 * @return array : (int <nb of deleted files>, array $erreurs)
	 */
	static public function deleteFiles(array $fileList, $nbOfDays)
	{
		$db = false; $origin = __CLASS__.'::'.__FUNCTION__;
		$erreurs   = array();
		$nbDeleted = 0;
		$timeAgo = time() - (60*60*24*$nbOfDays);
		$check      = strtotime("-$nbOfDays days");
		if ($db AND $timeAgo !== $check) {
			throw new Exception(sprintf('erreur calcul time one week ago : %d !== %d', $timeAgo, $check) );
			//				exit;
		}
		foreach ($fileList AS $file) {
			$File = new SplFileInfo($file);
			if (!$File->isFile() ) {
				$erreurs[] = 'File not found : ' . $File->getFilename();
				continue;
			}
			if ($db) {
				print 'Now : ' . strftime('%d/%m/%Y %H:%M:%S', time() );
				print 'makeTime : ' . strftime('%d/%m/%Y %H:%M:%S',
				$File->getMTime() ) . ' > one week ago : ' . strftime('%d/%m/%Y %H:%M:%S', $timeAgo) . ' ? ';
			}
			if ($File->getMTime() > $timeAgo) {
				if ($db) {
					print 'Fichier trop recent : ' . $File->getFilename();
				}
				continue;
			} else {
				if ($db) { print 'fichier a suppr : ' . $File->getFilename(); }
			}
			if ($db) { print "\n\r"; }
			$ok = unlink($file);
			if (!$ok) {
				$erreurs[] = 'echec suppression ' . $file;
			}
			$nbDeleted++;
		}
		
		return array($nbDeleted, $erreurs);
	}
}