public function cumulative(Request $request)
{
    $chamaId = $request->user()->chama_id ?? 1; // later from auth

    return DB::table('members as m')
        ->leftJoin('payments as p', 'm.member_id', '=', 'p.member_id')
        ->leftJoin('sheets as s', function($join) use ($chamaId) {
            $join->on('p.sheet_id', '=', 's.id')
                 ->where('s.chama_id', $chamaId);
        })
        ->where('s.is_excluded', false)
        ->where('m.chama_id', $chamaId)
        ->select(
            'm.member_id',
            'm.name',
            DB::raw('COALESCE(SUM(p.savings),0) as savings'),
            // ... all other sums
            DB::raw('COALESCE(SUM(p.savings + p.project + p.welfare + p.fine + p.others),0) as total')
        )
        ->groupBy('m.member_id', 'm.name')
        ->get();
}