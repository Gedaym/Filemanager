<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Romario
 * Date: 26/11/13
 * Time: 11:32
 * To change this template use File | Settings | File Templates.
 */

namespace GDM\FileManage;

class FileManager
{
    protected $root;
    protected $currentElement;
    protected $currentDetails;

    public function __construct($root)
    {
        if(!file_exists($root)&& !is_dir($root))
        {
            throw new \exception("Undefined path root!");
        }
        else
        {
            $this->root=$root;
            echo "<br>The path root is /".$this->root."<br />";
        }
    }



    /*generic operation*/
    public function SetCurrentElement($currentElement)
    {
        if($currentElement == $this->root)
        {
            throw new \Exception("The current element and the root can't be the same!");
        }
        else
        {
            if (file_exists($currentElement))
            {
                $this->currentElement=$currentElement;
            } else
            {
               throw new \exception("That file or folder doesn't exist!");
            }
        }
    }

    public function GetCurrentElement()
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined");
        }
        else
        {
            echo "<br> The current element is ".$this->currentElement."<br>";
        }
    }

    public function GetCurrentDetails() //Get the details of the current element
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined! ");
        }
        else
        {
            $current = $this->currentElement;
            $currentDetails = stat($current);
            $this->currentDetails = $currentDetails;
            $details = array("type"=>filetype($current), "modified"=>date('d/m/Y H:i:s', $currentDetails['mtime']), "size"=>$currentDetails['size']);
            echo "<br> Infos current element:"."<br>";
            foreach ($details as $key =>$value)
            {
                echo $key." : ".$value."<br>";
            }
        }
    }

    public function DeleteCurrentElement()
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            $currentElement=$this->currentElement;

            if(is_dir($currentElement))
            {
                $nbElement = 0;
                if ($dir = opendir($currentElement))
                {
                    while (($file = readdir($dir)) !== false && $nbElement==0)
                    {
                        if ($file!="." && $file!=".." )
                        {
                            $nbElement++;
                        }
                    }
                    closedir($dir);
                }
                if ($nbElement == 0)
                {
                    rmdir($currentElement);
                }
                else
                {
                    throw new \Exception("Cannot delete current element because is not empty! Use the function EmptyFolder before!");
                }
            }
            else
            {
                unlink($currentElement);
                echo "<br> The current element was deleted"."<br>";
            }
        }
    }

    public function RenameCurrentElement($newName)
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            $path=$this->currentElement;
            $fileName = strrpos($path, "/");
            $directory = substr($path, 0, $fileName);

            if(file_exists($directory."/".htmlspecialchars($newName)))
            {
                throw new \Exception("A file with the same name already exists");
            }
            else
            {
                rename($path,$directory."/".htmlspecialchars($newName));
                echo "<br>The current element has been renamed"."<br>";
            }
        }

    }

    public function MoveCurrentElement($destination)
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            if(is_dir(htmlspecialchars($destination)))
            {
                $path=$this->currentElement;
                $fileName = strrpos($path, "/");
                $directory = substr($path, 0, $fileName);

                if(is_file($this->currentElement))
                {
                    if(!file_exists(htmlspecialchars($destination)."/".$fileName))
                    {
                        rename($path, htmlspecialchars($destination)."/".$fileName);
                        var_dump($fileName);
                    }
                    else
                    {
                        throw new \Exception("A file with the same name already exists in the folder where you want to move it");
                    }
                }
                else
                {
                    $nbElement = 0;
                    if ($dir = opendir($path))
                    {
                        while (($file = readdir($dir)) !== false && $nbElement==0)
                        {
                            if ($file!="." && $file!=".." )
                            {
                                $nbElement++;
                            }
                        }
                        closedir($dir);
                    }
                    if ($nbElement == 0)
                    {
                        if(!file_exists(htmlspecialchars($destination)."/".$fileName))
                        {
                            rename($path, htmlspecialchars($destination)."/".$fileName);
                        }
                        else
                        {
                            throw new \Exception("A file with the same name already exists in the folder where you want to move it");
                        }
                    }
                    else
                    {
                        throw new \Exception("Cannot move current element because is not empty! Use the function EmptyFolder before!");
                    }
                }
            }
            else
            {
                throw new \Exception("The destination source must be a folder!");
            }
        }
    }
    /*End generic operation*/


    /*Specific operation for folders*/
    public function GetCurrentContent($recursive = TRUE) //List element in the current element, TRUE for recursion or FALSE if you don't need it
    {
        function GetElementRecursive($dir)
        {
            if (is_dir ($dir))
            {
                $dh = opendir ($dir);
            }
            while (($file = readdir($dh)) !== false )
            {
                if ($file !== '.' && $file !== '..')
                {
                    $path =$dir."/".$file;
                    if (is_dir ($path))
                    {
                        GetElementRecursive($path);
                        echo $path."<br/>";
                    }
                    else {
                        echo $path."<br/>";
                    }
                }
            }
            closedir ($dh);
        }

        function GetElement($dir)
        {
            if (is_dir ($dir))
            {
                $dh = opendir ($dir);
            }
            while (($file = readdir($dh)) !== false )
            {
                if ($file !== '.' && $file !== '..')
                {
                    $path =$dir."/".$file;
                    echo $path."<br/>";
                }
            }
            closedir ($dh);
        }

        if($this->currentElement !== NULL)
        {
            if(is_dir($this->currentElement))
            {
                if($recursive == TRUE)
                {
                    echo "<br/>Content current element with recursion :"."<br />";
                    GetElementRecursive($this->currentElement);
                }
                else
                {
                    echo "<br/>Content current element without recursion :"."<br />";
                    GetElement($this->currentElement);
                }
            }
            else
            {
                $path=$this->currentElement;
                $fileName = strrpos($path, "/");
                $directory = substr($path, 0, $fileName);
                if($recursive == TRUE)
                {
                    echo "<br/>Content current element :"."<br />";
                    GetElementRecursive($directory);
                }
                else
                {
                    echo "<br/>Content current element :"."<br />";
                    GetElement($directory);
                }
            }
         }
        else
        {
            throw new \Exception("The current element is not defined!");
        }
    }

    public function GetTreeDirectory()
    {
        if($this->currentElement !== NULL)
        {
            $dir_iterator = new \RecursiveDirectoryIterator(dirname($this->currentElement));
            $iterator = new \RecursiveIteratorIterator($dir_iterator);
            echo "<br> Arborescence :"."<br>";
            foreach ($iterator as $file)
            {
                echo $file."<br>";
            }
        }
        else
        {
            throw new \Exception("The current element is not defined!");
        }
    }

    public function EmptyFolder() //Recursively delete the content of a folder
    {
        function DeleteContent($dir)
        {
            if (is_dir ($dir))
            $dh = opendir ($dir);
            while (($file = readdir($dh)) !== false )
            {
                if ($file !== '.' && $file !== '..')
                {
                    $path =$dir.'/'.$file;
                    if (is_dir ($path))
                    {
                        DeleteContent($path);
                        rmdir($path);
                    }
                    else {
                        unlink($path);
                    }
                }
            }
            closedir ($dh);
        }

        if($this->currentElement !== NULL)
        {
            if(is_dir($this->currentElement))
            {
                DeleteContent($this->currentElement);
                echo "The current element has been emptied";
            }
            else
            {
                throw new \Exception("The current element is a file you can't empty it! ");
            }
        }
        else
        {
            throw new \Exception("The current element is not defined!");
        }
    }

    public function CreateFolder($folderName)
    {
        if($folderName !== NULL)
        {
            if($this->currentElement == NULL)
            {
                throw new \Exception("The current element is not defined!");
            }
            elseif(is_file($this->currentElement))
            {
                throw new \Exception("The current element is a file, use the function SetCurrentElement() to define a new current element! ");
            }
            else
            {
                if(!file_exists($this->currentElement."/".$folderName))
                {
                    mkdir($this->currentElement."/".$folderName);
                    echo "<br>Folder created with success"."<br>";
                }
                else
                {
                    throw new \Exception("That folder already exists!");
                }
            }
        }
        else
        {
            throw new \Exception("Argument missing, you must give a name to your folder");
        }
    }

    public function UploadFile($destination)
    {
        function RenameCopy($oldName, $dest, $i)
        {
            if(file_exists($dest."(".$i.")"))
            {
                $i++;
                RenameCopy($oldName,$dest, $i);
            }
            else
            {
                rename($oldName, $dest."(".$i.")");
                echo "Uploaded with success!";
            }
        }


        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            if(is_file($this->currentElement))
            {
                if(file_exists($destination) && is_dir($destination))
                {
                   $name = substr(strrchr($this->currentElement, '/'),1);;
                    $_FILES['file']['name'] = $name;
                    $_FILES['file']['tmp_name'] = $this->currentElement;
                    if(file_exists($destination."/".$name))
                    {
                        $dest = $destination."/".$name;
                        RenameCopy($_FILES['file']['tmp_name'], $dest, 1);
                    }
                    else
                    {
                        rename($_FILES['file']['tmp_name'],$destination."/".$name);
                        echo "Uploaded with success!";
                    }

                }
                else
                {
                    throw new \Exception("The destination is not a directory or he doesn't exist!");
                }
            }
            else
            {
                throw new \Exception("The current element is not a file!");
            }
        }
    }
    /*End specific operation for folders*/


    /*Specific operation for files*/
    public function GetFileContent()
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            if(is_file($this->currentElement))
            {
                $file=fopen($this->currentElement,"r+");
                $content=fread($file, 8192);
                fclose($file);
                echo "<br>Content of the current element : "."<br>".$content."<br>";
            }
            else
            {
                throw new \Exception("The current element is not a file!");
            }
        }
    }

    public function AddContentFile($content)
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            if(is_file($this->currentElement))
            {
                if(gettype($content) == "string" )
                {
                    $fp = fopen($this->currentElement, 'a+');
                    fwrite($fp, htmlspecialchars($content));
                    fclose($fp);
                    echo "<br> Content successfully added"."<br>";
                }
                else
                {
                    throw new \Exception("The content of the file must be a string!");
                }
            }
        }
    }

    public function ChangeContentFile($content)
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined!");
        }
        else
        {
            if(is_file($this->currentElement))
            {
                if(gettype($content) == "string" )
                {
                    $fp = fopen($this->currentElement, 'w');
                    fwrite($fp, htmlspecialchars($content));
                    fclose($fp);
                    echo "<br> Content successfully changed"."<br>";
                }
                else
                {
                    throw new \Exception("The content of the file must be a string!");
                }
            }
        }
    }

    public function Download()
    {
        if($this->currentElement == NULL)
        {
            throw new \Exception("The current element is not defined! BAKA!");
        }
        else
        {
            if(is_file($this->currentElement))
            {
                $filename = substr(strrchr($this->currentElement, '/'),1);
                header('Content-disposition: attachment; filename='.$filename);
                header ("Content-Type:application/octet-stream");
                $handle = fopen($this->currentElement, "r");
                if ($handle === false) {

                   throw new \Exception("Cannot open $this->currentElement");

                }
                while (!feof($handle)) {
                    $content= fread($handle, 8192);
                    echo $content;
                }

                fclose($handle);
                exit();
            }
            else
            {
                throw new \Exception("The current element is not a file!");
            }
        }
    }
    /*End specific operation for files*/
}