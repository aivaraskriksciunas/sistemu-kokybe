<?php 

namespace App\Services\Importing;

use App\Exceptions\Import\DeckImportException;
use App\Models\Deck;
use App\Models\Flashcard;
use Exception;

class AnkiImport extends SetImporter {

    private $separator = "\t";
    private $deck_name_col = null;
    private $question_column = 1;

    /**
     * Take a string and convert it into a list of Flashcards
     *
     * @param string $set
     * @return Flashcard[]
     */
    public function parse_set( string $set ): array
    {
        $lines = explode( "\n", $set );
        $flashcards = [];

        for ( $i = 0; $i < count( $lines ); $i++ )
        {
            if ( trim( $lines[$i] ) == '' ) continue;

            if ( $lines[$i][0] == '#' )
            {
                // If line begins with #, it is a header
                $this->parse_header( $lines[$i] );
                continue;
            }

            $cols = explode( $this->separator, $lines[$i] );
            if ( count( $cols ) < $this->question_column + 1 ) 
            {
                throw new DeckImportException( 'Line #' . ( $i + 1 ) . ' is malformed. It does not contain the question and answer word pairs.' );
            }

            $deck_name = 'Imported Anki deck';
            if ( $this->deck_name_col != null )
            {
                $deck_name = $cols[$this->deck_name_col - 1];
            }

            $card = new Flashcard();
            $card->question = $cols[$this->question_column - 1];
            $card->answer = $cols[$this->question_column];
            $flashcards[$deck_name][] = $card;
        }

        return $flashcards;
    }

    /**
     * Ensure that the result of parsing is one deck, even if the original file contain multiple decks
     *
     * @param array $decks
     * @return void
     */
    public function merge_parsed_decks_into_one( array $decks )
    {
        $all_cards = [];
        foreach ( $decks as $deck => $cards )
        {
            $all_cards = array_merge( $all_cards, $cards );
        }

        return [ 'Imported Anki deck' => $all_cards ];
    }

    private function parse_header( string $header ) 
    {
        $header_parts = explode( ':', $header, 2 );

        if ( $header_parts[0] == '#separator' )
        {
            if ( $header_parts[1] == 'tab' )
                $this->separator = "\t";
            else 
                $this->separator = $header_parts[1];
        }
        else if ( $header_parts[0] == '#deck column' )
        {
            try {
                $this->deck_name_col = intval( $header_parts[1] );
            }
            catch ( Exception $err ) {
                throw new DeckImportException( 'Provided file is corrupt: The numeric values in file descriptor are marlformed.' );
            }
        }

        if ( str_contains( $header_parts[0], 'column' ) )
        {
            // This column is used for column mapping
            $col = intval( $header_parts[1] );
            if ( $col > 0 )
            {
                if ( $col == $this->question_column )
                {
                    // Move the deck column by one, since this column is reserved for other purpose
                    $this->question_column++;
                }
            }
        }
    }

}