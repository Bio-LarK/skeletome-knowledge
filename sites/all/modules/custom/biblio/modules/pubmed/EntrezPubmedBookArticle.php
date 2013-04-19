<?php
/**
 * @file EntrezPubmedArticle.php
 * Provides a class for handling PubMed articles retrieved with EFetch.
 * Orginally writen by Stefan Freudenberg
 */

class BiblioEntrezPubmedBookArticle
{
  private $book_article;

  private $id;

  private $md5;

  private $biblio = array();

  /**
   * Stores the given SimpleXMLElement the PubMed ID and the md5 hash of the
   * serialized XML.
   *
   * @param $pubmedBookArticle
   *   a PubmedBookArticle element
   */
  public function __construct(SimpleXMLElement $pubmedBookArticle = NULL)
  {
    if ($pubmedBookArticle) {
      $this->setBookArticle($pubmedBookArticle);
    }
  }

  /**
   * Returns the PubMed ID of the article.
   *
   * @return int
   */
  public function setBookArticle(SimpleXMLElement $pubmedBookArticle)
  {
    $this->biblio = array();
    $this->book_article = $pubmedBookArticle->BookDocument;
    $this->pubmeddata = $pubmedBookArticle->PubmedBookData;
    $this->id = (int)$pubmedBookArticle->BookDocument->PMID;
    $this->md5 = md5($pubmedBookArticle->asXML());
    return $this;
  }
  public function getId()
  {
    return $this->id;
  }

  /**
   * Returns the md5 hash of the serialized XML.
   *
   * @return string
   */
  public function getMd5()
  {
    return $this->md5;
  }

  public function getBiblioAsObject() {
    return (object)$this->getBiblio();
  }

  /**
   * Returns article elements as an associative array suitable for import into
   * a biblio node.
   *
   * @return array
   */
  public function getBiblio()
  {
    if (empty($this->biblio)) {
        if (variable_get('biblio_auto_citekey', 1) ) {
          $citekey = '';
        }
        else {
          $citekey = $this->id;
        }

        $this->biblio = array(
        'title'           => (string)$this->book_article->Book->BookTitle,
        'biblio_citekey'  => $citekey,
        'biblio_pubmed_id' => $this->id,
        'biblio_pubmed_md5' => $this->md5,
        'biblio_contributors' => $this->contributors(),
        // MedlineCitations are always articles from journals or books
        'biblio_type'     => 102,
        'biblio_date'     => $this->date(),
        'biblio_year'     => substr($this->date(), 0, 4),
        'biblio_abst_e'   => $this->abst(),
        'biblio_custom1'  => "http://www.ncbi.nlm.nih.gov/pubmed/{$this->id}",
        'biblio_lang'     => $this->lang(),
      );
    }

    return $this->biblio;
  }

  /**
   * Returns the list of contributors for import obtained from the given
   * MedlineCitation element.
   *
   * @return array
   *   the contributors of the article
   */
  private function contributors()
  {
    $contributors = array();

    if (isset($this->book_article->Book->AuthorList->Author)) {
      foreach ($this->book_article->Book->AuthorList->Author as $author) {
        if (isset($author->CollectiveName)) {
          $category = 5; // corporate author
          $name = (string)$author->CollectiveName;
        } else {
          $category = 1; //primary (human) author
          $lastname = (string)$author->LastName;
          if (isset($author->ForeName)) {
            $name = $lastname . ', ' . (string)$author->ForeName;
          } elseif (isset($author->FirstName)) {
            $name = $lastname . ', ' . (string)$author->FirstName;
          } elseif (isset($author->Initials)) {
            $name = $lastname . ', ' . (string)$author->Initials;
          }
        }
        $contributors[] = array('name' => $name, 'auth_category' => $category);
      }
    }

    return $contributors;
  }

  /**
   * Returns the publication date obtained from the given MedlineCitation's
   * PubDate element. See the reference documentation for possible values:
   * http://www.nlm.nih.gov/bsd/licensee/elements_descriptions.html#pubdate
   * According to the above source it always begins with a four digit year.
   *
   * @return string
   *   the publication date of the article
   */
  private function date()
  {
    $pubDate = $this->book_article->Book->PubDate;
    $date = implode(' ', (array)$pubDate);
    return $date;
  }

  private function keywords() {
    return array();
  }

  private function lang() {
    if (isset($this->book_article->Language)) {
      return (string)$this->book_article->Language;
    }

  }

  private function abst() {
    if (isset($this->book_article->Abstract)) {
      $abst = '';
      foreach ($this->book_article->Abstract->AbstractText  as $text) {
        if (!empty($abst)) $abst .= "\n\n";
        $attrs = $text->attributes();
        if (isset($attrs['Label'])) {
          $abst .= $attrs['Label'] . ': ';
        }
        $abst .=  (string)$text ;
      }
      return $abst;
    }
  }
}
