<?php
/**
 * to be used with jquery plugin 'TreeView'
 * 
 * @example :
 * $DirIt = new RecursiveDirectoryIterator($ex_dir);
 * echo '<ul id="browser" class="filetree treeview-famfamfam">';
 * echo makeTreeView1($DirIt);
 * echo '</ul>';
 * @uses :
 * * jquery-1.3.js
 * * jquery.treeview-1.4/jquery.treeview.js
 * * specific js script to format the ul onload : $(<id of ul list to transform>).treeview();
 * * jquery.treeview-1.4/jquery.treeview.css
 * @return : html (every folder = 1 <li> enclosing a <ul> for contents)
 * * <ul>
 * *   <li><span class="folder">text</span> // span.folder "toggles" the ul within same li
 * *      <ul>
 * 	         <li><span class="file">text</span></li>
 *           <li>etc. // all contents of folder : dirs & files
 *           ...
 *         </ul>
 *      </li>      
 *      ...
 *    </ul>
 * @todo :
 * * dissallow toggle on empty dirs + specific icon
 * * add options : 
 * * * to determine which levels are open on first load,
 * * * etc.
 */
function makeTreeView1(RecursiveDirectoryIterator $DirIt, $level = 0) {
	$ret = '';
	$pad = str_repeat('', $level * 3); // /how to make identation while htmlspecialchars() is called ???
	$pad_li = $pad . str_repeat(' ', 3);

	$ret .=	$pad_li . '<li>' . '<span class="folder">';
	$ret .= $DirIt->current() . "({$DirIt->getType()} )\n\r"; // SplFileInfo !
	$ret .=	'</span>' . "\n\r" ;
	$ret .= $pad . '<ul>' . "\n\r";
	$n = 0;
	while ($DirIt->valid() ) {
		if ($DirIt->hasChildren() ) {
			$level++;
			$ret .= makeTreeView1($DirIt->getChildren(), $level);
			$level--;
		}
		if (!$DirIt->isDir() ) { // files !
			$ret .= $pad_li . '<li><span class="file">';
			$ret .= $DirIt . '(' . $DirIt->getType();
			$ret .= ' /level ' . $level . ' /nb ' . $n . ')</span></li>' . "\n\r";
		}
		$DirIt->next();
		$n++;
	}
	$ret .= '</ul>';
	$ret .= $pad_li . '</li>' .	"\n\r";
	return $ret;
}