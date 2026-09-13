<?php

namespace App\Controllers;

use App\Models\AuctionModel;
use App\Models\BidModel;
use App\Services\AuctionService;
use App\Services\BidService;

class Auctions extends BaseController
{
    protected AuctionModel $auctions;
    protected BidModel $bids;
    protected AuctionService $auctionService;
    protected BidService $bidService;

    public function __construct()
    {
        $this->auctions       = new AuctionModel();
        $this->bids           = new BidModel();
        $this->auctionService = new AuctionService();
        $this->bidService     = new BidService();
    }

    /**
     * Public auction listing — browse all auctions, filterable by status.
     */
    public function index()
    {
        $this->auctionService->runSweep();

        $status = $this->request->getGet('status');
        $builder = $this->auctions->select('auctions.*, users.full_name as seller_name')
            ->join('users', 'users.id = auctions.seller_id')
            ->orderBy('auctions.status = "live"', '', false)
            ->orderBy('auctions.end_time', 'ASC');

        if ($status && in_array($status, AuctionModel::STATUSES, true)) {
            $builder->where('auctions.status', $status);
        }

        $data = [
            'title'    => 'Live Auctions',
            'auctions' => $builder->paginate(9),
            'pager'    => $this->auctions->pager,
            'status'   => $status,
        ];

        return view('auctions/index', $data);
    }

    public function show(int $id)
    {
        $this->auctionService->runSweep();

        $auction = $this->auctions->withSeller($id);
        if (! $auction) {
            return redirect()->to('/auctions')->with('error', 'Auction not found.');
        }

        $data = [
            'title'       => $auction['title'],
            'auction'     => $auction,
            'leader'      => $this->bidService->currentLeader($id),
            'history'     => $this->bids->latestFirst($id, 25),
            'minNextBid'  => $this->minNextBid($auction),
        ];

        return view('auctions/show', $data);
    }

    public function create()
    {
        return view('auctions/create', ['title' => 'Create Auction']);
    }

    public function store()
    {
        $rules = [
            'title'          => 'required|min_length[3]|max_length[200]',
            'description'    => 'permit_empty',
            'starting_price' => 'required|decimal|greater_than[0]',
            'reserve_price'  => 'permit_empty|decimal|greater_than_equal_to[0]',
            'min_increment'  => 'required|decimal|greater_than[0]',
            'start_time'     => 'required|valid_date',
            'end_time'       => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (strtotime($this->request->getPost('end_time')) <= strtotime($this->request->getPost('start_time'))) {
            return redirect()->back()->withInput()->with('error', 'End time must be after start time.');
        }

        $imageName = $this->handleImageUpload();

        $status = strtotime($this->request->getPost('start_time')) <= time() ? 'live' : 'upcoming';

        $id = $this->auctionService->create([
            'seller_id'      => $this->currentUserId(),
            'title'          => $this->request->getPost('title'),
            'description'    => $this->request->getPost('description'),
            'starting_price' => $this->request->getPost('starting_price'),
            'reserve_price'  => $this->request->getPost('reserve_price') ?: null,
            'min_increment'  => $this->request->getPost('min_increment'),
            'start_time'     => $this->request->getPost('start_time'),
            'end_time'       => $this->request->getPost('end_time'),
            'status'         => $status,
            'image'          => $imageName,
        ]);

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Could not create auction.');
        }

        return redirect()->to('/auctions/' . $id)->with('success', 'Auction created!');
    }

    public function edit(int $id)
    {
        $auction = $this->auctions->find($id);
        if (! $auction || ((int) $auction['seller_id'] !== $this->currentUserId() && $this->currentRole() !== 'admin')) {
            return redirect()->to('/dashboard')->with('error', 'You cannot edit this auction.');
        }

        return view('auctions/edit', ['title' => 'Edit Auction', 'auction' => $auction]);
    }

    public function update(int $id)
    {
        $auction = $this->auctions->find($id);
        if (! $auction || ((int) $auction['seller_id'] !== $this->currentUserId() && $this->currentRole() !== 'admin')) {
            return redirect()->to('/dashboard')->with('error', 'You cannot edit this auction.');
        }

        $rules = [
            'title'          => 'required|min_length[3]|max_length[200]',
            'starting_price' => 'required|decimal|greater_than[0]',
            'min_increment'  => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'reserve_price' => $this->request->getPost('reserve_price') ?: null,
            'min_increment' => $this->request->getPost('min_increment'),
        ];

        // Only allow editing price/timing before the auction goes live.
        if ($auction['status'] === 'upcoming') {
            $data['starting_price'] = $this->request->getPost('starting_price');
            $data['start_time']     = $this->request->getPost('start_time');
            $data['end_time']       = $this->request->getPost('end_time');
            $data['original_end_time'] = $this->request->getPost('end_time');
        }

        $newImage = $this->handleImageUpload();
        if ($newImage) {
            $data['image'] = $newImage;
        }

        $this->auctionService->update($id, $data);

        return redirect()->to('/auctions/' . $id)->with('success', 'Auction updated.');
    }

    public function mine()
    {
        $data = [
            'title'    => 'My Auctions',
            'auctions' => $this->auctions->where('seller_id', $this->currentUserId())
                ->orderBy('created_at', 'DESC')
                ->findAll(),
        ];

        return view('auctions/mine', $data);
    }

    private function minNextBid(array $auction): float
    {
        $leader = $this->bidService->currentLeader((int) $auction['id']);

        return $leader
            ? (float) $leader['amount'] + (float) $auction['min_increment']
            : (float) $auction['starting_price'];
    }

    private function handleImageUpload(): ?string
    {
        $file = $this->request->getFile('image');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads', $newName);

        return $newName;
    }
}
