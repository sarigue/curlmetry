<?php

namespace Curlmetry\Test\TestCase;

use Curlmetry\Psr\StringStream;
use Curlmetry\Test\CurlmetryTestCase;

class StringStreamTest extends CurlmetryTestCase
{
    /**
     * Test that getContents returns the remaining content from the current position.
     */
    public function testGetContentsReturnsRemainingContent()
    {
        $stream = new StringStream('Sample content');
        $stream->seek(7); // Move position to 7
        $this->assertEquals('content', $stream->getContents());
    }

    /**
     * Test that getContents updates the position to the end of the string after invocation.
     */
    public function testGetContentsUpdatesPositionToEnd()
    {
        $stream = new StringStream('Another example');
        $stream->seek(8); // Move position to 8
        $stream->getContents();
        $this->assertTrue($stream->eof());
    }

    /**
     * Test that getContents returns the entire content if called at the initial position.
     */
    public function testGetContentsReturnsEntireContentFromStart()
    {
        $stream = new StringStream('Full content');
        $this->assertEquals('Full content', $stream->getContents());
    }

    /**
     * Test that getContents returns an empty string if already at the end of the stream.
     */
    public function testGetContentsReturnsEmptyStringAtEnd()
    {
        $stream = new StringStream('End test');
        $stream->seek(strlen('End test')); // Move position to the end
        $this->assertEquals('', $stream->getContents());
    }

    /**
     * Test that getContents returns an empty string for an empty stream.
     */
    public function testGetContentsReturnsEmptyStringForEmptyStream()
    {
        $stream = new StringStream('');
        $this->assertEquals('', $stream->getContents());
    }

    /**
     * Test that eof initially returns true for an empty stream.
     */
    public function testEofInitiallyTrueForEmptyStream()
    {
        $stream = new StringStream('');
        $this->assertTrue($stream->eof());
    }

    /**
     * Test that eof initially returns false for a non-empty stream.
     */
    public function testEofInitiallyFalseForNonEmptyStream()
    {
        $stream = new StringStream('Content');
        $this->assertFalse($stream->eof());
    }

    /**
     * Test that eof returns true after reading the entire stream content.
     */
    public function testEofUpdatesAfterReadOperation()
    {
        $stream = new StringStream('Read position');
        $stream->read(strlen('Read position'));
        $this->assertTrue($stream->eof());
    }

    /**
     * Test that eof returns false when new content is written to the stream.
     */
    public function testEofAfterWriteOperation()
    {
        $stream = new StringStream('Old content', true);
        $stream->write(' New content');
        $this->assertTrue($stream->eof());
    }

    /**
     * Test that getSize returns 0 for an empty stream.
     */
    public function testGetSizeWithEmptyStream()
    {
        $stream = new StringStream('');
        $this->assertEquals(0, $stream->getSize());
    }

    /**
     * Test that getSize returns the correct size for a non-empty stream.
     */
    public function testGetSizeWithNonEmptyStream()
    {
        $stream = new StringStream('Stream content');
        $this->assertEquals(14, $stream->getSize());
    }

    /**
     * Test that getSize reflects updates when content is written to the stream.
     */
    public function testGetSizeAfterWriteOperation()
    {
        $stream = new StringStream('Initial');
        $stream->write(' content');
        $this->assertEquals(15, $stream->getSize());
    }

    public function testConstructorWithDefaultValues()
    {
        $stream = new StringStream('Test content');

        $this->assertFalse($stream->isWritable());
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('Test content', (string)$stream);
        $this->assertTrue($stream->eof());
    }

    /**
     * Test __toString method returns the full string content.
     */
    public function testToStringReturnsString()
    {
        $stream = new StringStream('Example content');

        $this->assertEquals('Example content', $stream->__toString());
    }

    /**
     * Test __toString method updates the position to the end of the string.
     */
    public function testToStringUpdatesPosition()
    {
        $stream = new StringStream('Position content');

        $stream->__toString();
        $this->assertEquals(strlen('Position content'), $stream->tell());
    }

    public function testConstructorWithWritableFlag()
    {
        $stream = new StringStream('Writable content', true);

        $this->assertEquals('Writable content', (string)$stream);
        $this->assertTrue($stream->isWritable());
        $this->assertEquals(strlen('Writable content'), $stream->tell());
    }

    public function testConstructorStringContent()
    {
        $stream = new StringStream(12345);

        $this->assertEquals('12345', (string)$stream);
        $this->assertEquals(5, $stream->getSize());
    }

    public function testStreamOperations()
    {
        $stream = new StringStream('Hello');

        $this->assertEquals(5, $stream->getSize());
        $this->assertEquals('Hello', (string)$stream);
        $this->assertTrue($stream->eof());

        $stream->rewind();
        $this->assertEquals('H', $stream->read(1));

        $stream->seek(0, SEEK_END);
        $stream->write(' World');
        $this->assertEquals('Hello World', (string)$stream);
    }

    /**
     * Test that tell initially returns position 0.
     */
    public function testTellInitiallyAtZero()
    {
        $stream = new StringStream('Initial content');
        $this->assertEquals(0, $stream->tell());
    }

    /**
     * Test that rewind resets the position to the start for a non-empty stream.
     */
    public function testRewindResetsPositionForNonEmptyStream()
    {
        $stream = new StringStream('Some content');
        $stream->seek(5); // Move to a non-zero position
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    /**
     * Test that rewind resets the position to the start for an empty stream.
     */
    public function testRewindResetsPositionForEmptyStream()
    {
        $stream = new StringStream('');
        $stream->seek(5); // Attempt to move to a non-zero position
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    /**
     * Test that rewind does not alter the stream content.
     */
    public function testRewindDoesNotAlterContent()
    {
        $stream = new StringStream('Content remains unchanged');
        $stream->seek(8); // Move to a non-zero position
        $stream->rewind();
        $this->assertEquals('Content remains unchanged', (string)$stream);
    }

    /**
     * Test that tell correctly returns the position after a read operation.
     */
    public function testTellAfterReadOperation()
    {
        $stream = new StringStream('Reading position');
        $stream->read(8);
        $this->assertEquals(8, $stream->tell());
    }

    /**
     * Test that tell correctly returns the position after a write operation.
     */
    public function testTellAfterWriteOperation()
    {
        $stream = new StringStream('Write position', true);
        $stream->write(' test');
        $this->assertEquals(strlen('Write position test'), $stream->tell());
    }

    /**
     * Test that tell reflects the correct position after seeking.
     */
    public function testTellAfterSeek()
    {
        $stream = new StringStream('Seeking position');
        $stream->seek(7); // Seek to index 7
        $this->assertEquals(7, $stream->tell());
    }

    /**
     * Test that isSeekable returns true for a stream with default initialization.
     */
    public function testIsSeekableForDefaultStream()
    {
        $stream = new StringStream('Default content');
        $this->assertTrue($stream->isSeekable());
    }

    /**
     * Test that isWritable returns false for a stream initialized with default writable flag.
     */
    public function testIsWritableDefaultsToFalse()
    {
        $stream = new StringStream('Default content');
        $this->assertFalse($stream->isWritable());
    }

    /**
     * Test that isWritable returns true for a stream initialized with writable flag set to true.
     */
    public function testIsWritableWhenFlagSetToTrue()
    {
        $stream = new StringStream('Writable content', true);
        $this->assertTrue($stream->isWritable());
    }

    /**
     * Test that isSeekable returns true for a writable stream.
     */
    public function testIsSeekableForWritableStream()
    {
        $stream = new StringStream('Writable content', true);
        $this->assertTrue($stream->isSeekable());
    }

    /**
     * Test the consistency of isSeekable during seeking operations.
     */
    public function testIsSeekableDuringSeek()
    {
        $stream = new StringStream('Stream content');
        $this->assertTrue($stream->isSeekable());

        $stream->seek(5);
        $this->assertTrue($stream->isSeekable());

        $stream->seek(0);
        $this->assertTrue($stream->isSeekable());
    }

    /**
     * Test that seek with SEEK_SET moves to the correct position.
     */
    public function testSeekWithSeekSetMovesToCorrectPosition()
    {
        $stream = new StringStream('Test seek content');
        $stream->seek(5, SEEK_SET);
        $this->assertEquals(5, $stream->tell());
    }

    /**
     * Test that seek with SEEK_CUR moves to the correct position relative to the current position.
     */
    public function testSeekWithSeekCurMovesToCorrectPosition()
    {
        $stream = new StringStream('Test seek content');
        $stream->seek(5, SEEK_SET); // Move to position 5
        $stream->seek(3, SEEK_CUR); // Move 3 positions forward
        $this->assertEquals(8, $stream->tell());
    }

    /**
     * Test that seek with SEEK_END moves to the position relative to the stream's end.
     */
    public function testSeekWithSeekEndMovesToCorrectPosition()
    {
        $stream = new StringStream('Test seek content');
        $stream->seek(4, SEEK_END); // Move 4 positions back from the end
        $this->assertEquals(strlen('Test seek content') - 4, $stream->tell());
    }

    /**
     * Test that seeking beyond the valid range clamps the position to a valid range.
     */
    public function testSeekBeyondBoundsClampsPositionToValidRange()
    {
        $stream = new StringStream('Short content');
        $stream->seek(-10, SEEK_SET); // Attempt to seek before the start
        $this->assertEquals(0, $stream->tell());

        $stream->seek(50, SEEK_SET); // Attempt to seek past the end
        $this->assertEquals(strlen('Short content'), $stream->tell());
    }

    /**
     * Test that seek does not fail with an empty content stream.
     */
    public function testSeekDoesNotFailWithEmptyContent()
    {
        $stream = new StringStream('');
        $stream->seek(5, SEEK_SET); // Seek an empty stream
        $this->assertEquals(0, $stream->tell()); // Position should remain 0
    }

    /**
     * Test that a stream is always readable when initialized with content.
     */
    public function testIsReadableWithContent()
    {
        $stream = new StringStream('Readable content');
        $this->assertTrue($stream->isReadable());
    }

    /**
     * Test that a stream remains readable when positioned at the end of the content.
     */
    public function testIsReadableAtEndOfContent()
    {
        $stream = new StringStream('End of readable content');
        $stream->seek(strlen('End of readable content')); // Move to the end of the content
        $this->assertTrue($stream->isReadable());
    }

    /**
     * Test that an empty stream is also readable.
     */
    public function testIsReadableWithEmptyStream()
    {
        $stream = new StringStream('');
        $this->assertTrue($stream->isReadable());
    }

    /**
     * Test that read returns the correct content when reading from the beginning.
     */
    public function testReadReturnsCorrectContentFromBeginning()
    {
        $stream = new StringStream('Read test content');
        $this->assertEquals('Read', $stream->read(4));
    }

    /**
     * Test that read moves the position forward as content is read.
     */
    public function testReadAdvancesPosition()
    {
        $stream = new StringStream('Position test');
        $stream->read(4); // Read first 4 characters
        $this->assertEquals(4, $stream->tell());
        $this->assertEquals('tion', $stream->read(4));
    }

    /**
     * Test that read returns an empty string when attempting to read beyond the end.
     */
    public function testReadReturnsEmptyStringWhenBeyondEnd()
    {
        $stream = new StringStream('End test');
        $stream->read(50); // Try to read beyond the length of the string
        $this->assertEquals('', $stream->read(10)); // Should return empty string
    }

    /**
     * Test that read returns partial content when reading near the end.
     */
    public function testReadReturnsPartialContentNearEnd()
    {
        $stream = new StringStream('Partial test');
        $stream->seek(3, SEEK_END); // Move to 3 characters before the end
        $this->assertEquals('est', $stream->read(10)); // Only 3 characters left
    }

    /**
     * Test that read returns an empty string when the stream is empty.
     */
    public function testReadWithEmptyStream()
    {
        $stream = new StringStream('');
        $this->assertEquals('', $stream->read(5)); // Nothing to read
    }

    /**
     * Test that consecutive reads return correct chunks of content.
     */
    public function testConsecutiveReadsReturnCorrectChunks()
    {
        $stream = new StringStream('Chunk test');
        $this->assertEquals('Chunk', $stream->read(5));
        $this->assertEquals(' test', $stream->read(5));
        $this->assertTrue($stream->eof());
    }
}
