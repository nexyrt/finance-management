interface Sparkline30DaysProps {
    data: number[];
    width?: number;
    height?: number;
    className?: string;
}

export default function Sparkline30Days({ data, width = 200, height = 44, className = '' }: Sparkline30DaysProps) {
    const hasData = data && data.length >= 2 && data.some(v => v !== 0);

    if (!hasData) {
        return (
            <svg width="100%" height={height} className={className} viewBox={`0 0 ${width} ${height}`} preserveAspectRatio="none">
                <line
                    x1={0} y1={height / 2}
                    x2={width} y2={height / 2}
                    stroke="currentColor"
                    strokeOpacity={0.15}
                    strokeWidth={1}
                    strokeDasharray="4 3"
                />
            </svg>
        );
    }

    const padV = 5;
    const padH = 2;
    const min = Math.min(...data);
    const max = Math.max(...data);
    const range = max - min || 1;
    const drawW = width - padH * 2;
    const drawH = height - padV * 2;

    const points = data.map((v, i) => {
        const x = padH + (i / (data.length - 1)) * drawW;
        const y = padV + (1 - (v - min) / range) * drawH;
        return [x, y] as [number, number];
    });

    // Smooth curve via cubic bezier
    const pathD = points.reduce((acc, [x, y], i) => {
        if (i === 0) return `M${x.toFixed(2)},${y.toFixed(2)}`;
        const [px, py] = points[i - 1];
        const cpx = (px + x) / 2;
        return `${acc} C${cpx.toFixed(2)},${py.toFixed(2)} ${cpx.toFixed(2)},${y.toFixed(2)} ${x.toFixed(2)},${y.toFixed(2)}`;
    }, '');

    const lastPoint = data[data.length - 1] ?? 0;
    const isPositive = lastPoint >= 0;
    const strokeColor = isPositive ? '#34d399' : '#f87171';
    const fillColor = isPositive ? '#10b981' : '#ef4444';

    const [lastX, lastY] = points[points.length - 1];
    const [firstX] = points[0];

    // Area fill: close down to baseline
    const areaD = `${pathD} L${lastX.toFixed(2)},${(height - padV).toFixed(2)} L${firstX.toFixed(2)},${(height - padV).toFixed(2)} Z`;

    const gradId = `sg-${Math.random().toString(36).slice(2, 6)}`;

    return (
        <svg
            width="100%"
            height={height}
            viewBox={`0 0 ${width} ${height}`}
            preserveAspectRatio="none"
            className={className}
            overflow="visible"
        >
            <defs>
                <linearGradient id={gradId} x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor={fillColor} stopOpacity={0.3} />
                    <stop offset="100%" stopColor={fillColor} stopOpacity={0} />
                </linearGradient>
            </defs>

            {/* Area fill */}
            <path d={areaD} fill={`url(#${gradId})`} />

            {/* Line */}
            <path
                d={pathD}
                fill="none"
                stroke={strokeColor}
                strokeWidth={1.8}
                strokeLinecap="round"
                strokeLinejoin="round"
            />

            {/* Glow dot at last point */}
            <circle cx={lastX} cy={lastY} r={5} fill={strokeColor} opacity={0.15} />
            <circle cx={lastX} cy={lastY} r={2.5} fill={strokeColor} />
        </svg>
    );
}
