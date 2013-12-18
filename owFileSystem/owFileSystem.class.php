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
	
	/**
	 * deplace le fichier $local_filepath vers le dossier $sauv_dir
	 *
	 * si $sauv_dir n'existe pas, nous tentons de le creer
	 * @param $local_filepath : absolute path to filename
	 * @param $sauv_dir       : absolute path to dirname (trailing dir sep is optional)
	 */
	public static function archiveFile($local_filepath, $sauv_dir, $sauv_filename = null)
	{
		$iLastIndex = strlen($sauv_dir)-1;
		if ($iLastIndex != strrpos($sauv_dir, DIRECTORY_SEPARATOR) ) {
			$sauv_dir .= DIRECTORY_SEPARATOR;
		}

		if (!$sauv_filename) {
			$sauv_filename = basename($local_filepath);
		}
		
		if (!is_dir($sauv_dir) ) {
			if(!mkdir($sauv_dir, 0700) ) {
				throw new Exception (sprintf(
				'%s : dossier manquant : %s',
				__CLASS__, $sauv_dir
				));
			}
		}
		
		$sauvFile = $sauv_dir . $sauv_filename;
		if (!rename($local_filepath, $sauvFile)) {
			throw new Exception(printf(
			'%s : le deplacement de %s vers %s a ?choue<br/>\n',
			__CLASS__, $local_filepath, $sauvFile)
			);
		}
		// unlink($local_filepath);
		return $sauvFile;
	} // /archiveFile()
}