<?php

namespace App\Services;

/**
 * Generic array-backed binary Max-Heap.
 *
 * Used to track the highest bid in an auction: the item that compares
 * "greatest" always sits at the root and can be read/extracted in O(1)/O(log n).
 *
 * Comparator contract: fn(a, b): int — return > 0 if $a outranks $b (should end
 * up closer to the root), < 0 if $b outranks $a, 0 if equal rank.
 *
 * For bids we rank by amount DESC, then created_at ASC, then id ASC — i.e. the
 * highest amount wins, and among equal amounts the earliest bid wins.
 */
class MaxHeap
{
    /** @var array<int, mixed> */
    private array $data = [];

    private \Closure $compare;

    public function __construct(callable $compare)
    {
        $this->compare = \Closure::fromCallable($compare);
    }

    /**
     * Build a heap from an existing array in O(n) (bottom-up heapify).
     *
     * @param array<int, mixed> $items
     */
    public static function fromArray(array $items, callable $compare): self
    {
        $heap = new self($compare);
        $heap->data = array_values($items);

        for ($i = (int) floor(count($heap->data) / 2) - 1; $i >= 0; $i--) {
            $heap->siftDown($i);
        }

        return $heap;
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function count(): int
    {
        return count($this->data);
    }

    /**
     * O(1) look at the current max without removing it.
     */
    public function peek(): mixed
    {
        return $this->data[0] ?? null;
    }

    /**
     * O(log n) insert — append then sift up.
     */
    public function insert(mixed $item): void
    {
        $this->data[] = $item;
        $this->siftUp(count($this->data) - 1);
    }

    /**
     * O(log n) remove and return the max (the root).
     */
    public function extractMax(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        $max  = $this->data[0];
        $last = array_pop($this->data);

        if ($this->data !== []) {
            $this->data[0] = $last;
            $this->siftDown(0);
        }

        return $max;
    }

    /**
     * Drain the heap into a fully ordered array (highest first).
     * Equivalent to repeated extractMax() calls — O(n log n).
     *
     * @return array<int, mixed>
     */
    public function toSortedArray(): array
    {
        $clone   = clone $this;
        $ordered = [];

        while (! $clone->isEmpty()) {
            $ordered[] = $clone->extractMax();
        }

        return $ordered;
    }

    private function siftUp(int $i): void
    {
        while ($i > 0) {
            $parent = (int) floor(($i - 1) / 2);

            if (($this->compare)($this->data[$i], $this->data[$parent]) > 0) {
                $this->swap($i, $parent);
                $i = $parent;
            } else {
                break;
            }
        }
    }

    private function siftDown(int $i): void
    {
        $count = count($this->data);

        while (true) {
            $left    = 2 * $i + 1;
            $right   = 2 * $i + 2;
            $largest = $i;

            if ($left < $count && ($this->compare)($this->data[$left], $this->data[$largest]) > 0) {
                $largest = $left;
            }
            if ($right < $count && ($this->compare)($this->data[$right], $this->data[$largest]) > 0) {
                $largest = $right;
            }
            if ($largest === $i) {
                break;
            }

            $this->swap($i, $largest);
            $i = $largest;
        }
    }

    private function swap(int $a, int $b): void
    {
        [$this->data[$a], $this->data[$b]] = [$this->data[$b], $this->data[$a]];
    }
}
