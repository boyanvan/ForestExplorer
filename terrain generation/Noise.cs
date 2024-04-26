using UnityEngine;

public static class Noise
{
    public static float[,] GenerateNoisemap(NoisemapSettings ns)
    {
        int width = ns.size.x;
        int height = ns.size.y;

        int scale = width;

        float[,] noisemap = new float[width, height];

        float minNoiseHeight = float.MinValue;
        float maxNoiseHeight = float.MaxValue;

        for (int y = 0; y < height; y++)
        {
            for (int x = 0; x < width; x++)
            {
                float amplitude = 1f;
                float frequency = ns.frequency;
                float noiseHeight = 0f;

                for (int i = 0; i < ns.octaves; i++)
                {
                    float xCoord = ns.origin.x + (float)x / scale * frequency;
                    float yCoord = ns.origin.y + (float)y / scale * frequency;

                    float perlinValue = Mathf.PerlinNoise(xCoord, yCoord) * 2 - 1;

                    noiseHeight += perlinValue * amplitude;

                    amplitude *= ns.amplitudeStep;
                    frequency *= ns.frequencyStep;
                }

                noiseHeight = Mathf.Pow(noiseHeight, ns.power);

                if (noiseHeight > minNoiseHeight)
                    minNoiseHeight = noiseHeight;
                if (noiseHeight < maxNoiseHeight)
                    maxNoiseHeight = noiseHeight;

                noiseHeight = Mathf.Pow(noiseHeight, ns.power);
                noisemap[x, y] = noiseHeight;
            }
        }

        for (int y = 0; y < height; y++)
        {
            for (int x = 0; x < width; x++)
            {
                noisemap[x, y] = Mathf.InverseLerp(minNoiseHeight, maxNoiseHeight, noisemap[x, y]);
                noisemap[x, y] = (float)System.Math.Round(noisemap[x, y], ns.round);
            }
        }

        return noisemap;
    }
}

[System.Serializable]
public class NoisemapSettings
{
    [SerializeField] private Vector2Int _size;
    public Vector2Int size => _size;

    [SerializeField] private Vector2 _origin;
    public Vector2 origin => _origin;

    [SerializeField] private float _frequency;
    public float frequency => _frequency;

    [SerializeField] private float _power;
    public float power => _power;

    [SerializeField] private int _octaves;
    public int octaves => _octaves;

    [Range(0f, 1f)]
    [SerializeField] private float _amplitudeStep;
    public float amplitudeStep => _amplitudeStep;

    [SerializeField] private float _frequencyStep;
    public float frequencyStep => _frequencyStep;

    [SerializeField] private int _round;
    public int round => _round;
}